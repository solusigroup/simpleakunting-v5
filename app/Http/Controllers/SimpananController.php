<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\JenisSimpanan;
use App\Models\Simpanan;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SimpananController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Simpanan::with(['anggota', 'jenisSimpanan']);

        // Filter by anggota
        if ($request->filled('anggota')) {
            $query->where('id_anggota', $request->anggota);
        }

        // Filter by jenis simpanan
        if ($request->filled('jenis')) {
            $query->where('id_jenis_simpanan', $request->jenis);
        }

        // Filter by tanggal
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        $simpanan = $query->orderBy('tanggal', 'desc')->paginate(15);
        $jenisSimpanan = JenisSimpanan::active()->get();
        $anggotaList = Anggota::aktif()->get();

        return view('simpanan.index', compact('simpanan', 'jenisSimpanan', 'anggotaList'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $anggotaList = Anggota::aktif()->get();
        $jenisSimpanan = JenisSimpanan::active()->get();
        $akunKasBank = Akun::where('tipe_akun', 'like', '%Kas%')
            ->orWhere('tipe_akun', 'like', '%Bank%')
            ->orWhere('nama_akun', 'like', '%Kas%')
            ->orWhere('nama_akun', 'like', '%Bank%')
            ->get();

        return view('simpanan.create', compact('anggotaList', 'jenisSimpanan', 'akunKasBank'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_anggota' => 'required|exists:anggota,id_anggota',
            'id_jenis_simpanan' => 'required|exists:jenis_simpanan,id_jenis_simpanan',
            'tanggal' => 'required|date',
            'jenis_transaksi' => 'required|in:setor,tarik',
            'jumlah' => 'required|numeric|min:1',
            'akun_kas_bank' => 'required|exists:akun,kode_akun',
            'keterangan' => 'nullable|string',
        ]);

        // Validate withdrawal
        if ($validated['jenis_transaksi'] === 'tarik') {
            $jenisSimpanan = JenisSimpanan::find($validated['id_jenis_simpanan']);
            
            // Only sukarela and deposito can be withdrawn
            if (!in_array($jenisSimpanan->tipe, ['sukarela', 'deposito'])) {
                return back()->withErrors(['jenis_transaksi' => 'Simpanan ' . $jenisSimpanan->nama_simpanan . ' tidak dapat ditarik.'])->withInput();
            }

            // Check balance
            $saldo = Simpanan::where('id_anggota', $validated['id_anggota'])
                ->where('id_jenis_simpanan', $validated['id_jenis_simpanan'])
                ->selectRaw('SUM(CASE WHEN jenis_transaksi = "setor" THEN jumlah ELSE -jumlah END) as saldo')
                ->value('saldo') ?? 0;

            if ($validated['jumlah'] > $saldo) {
                return back()->withErrors(['jumlah' => 'Saldo tidak mencukupi. Saldo saat ini: Rp ' . number_format($saldo, 0, ',', '.')])->withInput();
            }
        }

        DB::beginTransaction();
        try {
            // Generate nomor transaksi
            $tahun = date('Y');
            $bulan = date('m');
            $prefix = "SIM-{$tahun}{$bulan}-";
            $lastSimpanan = Simpanan::where('no_transaksi', 'like', $prefix . '%')
                ->orderBy('no_transaksi', 'desc')
                ->first();
            $newNumber = $lastSimpanan ? (int)substr($lastSimpanan->no_transaksi, -4) + 1 : 1;
            $validated['no_transaksi'] = $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $validated['created_by'] = auth()->id();

            $jenisSimpanan = JenisSimpanan::find($validated['id_jenis_simpanan']);
            $anggota = Anggota::find($validated['id_anggota']);

            // Create jurnal
            $jurnal = Jurnal::create([
                'no_transaksi' => $validated['no_transaksi'],
                'tanggal' => $validated['tanggal'],
                'deskripsi' => ($validated['jenis_transaksi'] === 'setor' ? 'Setoran ' : 'Penarikan ') . 
                               $jenisSimpanan->nama_simpanan . ' - ' . $anggota->nama_lengkap,
                'sumber_jurnal' => 'Simpanan',
                'is_locked' => false,
            ]);

            // Create jurnal details
            if ($validated['jenis_transaksi'] === 'setor') {
                // Setor: Dr. Kas/Bank, Cr. Simpanan
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $validated['akun_kas_bank'],
                    'debit' => $validated['jumlah'],
                    'kredit' => 0,
                ]);
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $jenisSimpanan->akun_simpanan,
                    'debit' => 0,
                    'kredit' => $validated['jumlah'],
                ]);
            } else {
                // Tarik: Dr. Simpanan, Cr. Kas/Bank
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $jenisSimpanan->akun_simpanan,
                    'debit' => $validated['jumlah'],
                    'kredit' => 0,
                ]);
                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $validated['akun_kas_bank'],
                    'debit' => 0,
                    'kredit' => $validated['jumlah'],
                ]);
            }

            $validated['id_jurnal'] = $jurnal->id_jurnal;
            Simpanan::create($validated);

            DB::commit();

            return redirect()->route('simpanan.index')
                ->with('success', 'Transaksi simpanan berhasil disimpan dengan nomor: ' . $validated['no_transaksi']);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Terjadi kesalahan: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $simpanan = Simpanan::with(['anggota', 'jenisSimpanan', 'jurnal.details.akun'])->findOrFail($id);
        return view('simpanan.show', compact('simpanan'));
    }

    /**
     * Show the form for editing the specified resource (not typically used for simpanan).
     */
    public function edit(string $id)
    {
        return redirect()->route('simpanan.index')
            ->with('error', 'Transaksi simpanan tidak dapat diedit.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        return redirect()->route('simpanan.index')
            ->with('error', 'Transaksi simpanan tidak dapat diedit.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $simpanan = Simpanan::with('jurnal')->findOrFail($id);
        
        // Check if jurnal is locked
        if ($simpanan->jurnal && $simpanan->jurnal->is_locked) {
            return redirect()->route('simpanan.index')
                ->with('error', 'Transaksi tidak dapat dihapus karena jurnal sudah dikunci.');
        }

        DB::beginTransaction();
        try {
            // Delete jurnal and details
            if ($simpanan->jurnal) {
                JurnalDetail::where('id_jurnal', $simpanan->id_jurnal)->delete();
                $simpanan->jurnal->delete();
            }
            $simpanan->delete();

            DB::commit();
            return redirect()->route('simpanan.index')
                ->with('success', 'Transaksi simpanan berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('simpanan.index')
                ->with('error', 'Gagal menghapus transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Form setoran simpanan
     */
    public function setor()
    {
        $anggotaList = Anggota::aktif()->get();
        $jenisSimpanan = JenisSimpanan::active()->get();
        $akunKasBank = Akun::where('tipe_akun', 'like', '%Kas%')
            ->orWhere('tipe_akun', 'like', '%Bank%')
            ->orWhere('nama_akun', 'like', '%Kas%')
            ->orWhere('nama_akun', 'like', '%Bank%')
            ->get();

        return view('simpanan.setor', compact('anggotaList', 'jenisSimpanan', 'akunKasBank'));
    }

    /**
     * Form penarikan simpanan
     */
    public function tarik()
    {
        $anggotaList = Anggota::aktif()->get();
        $jenisSimpanan = JenisSimpanan::active()->whereIn('tipe', ['sukarela', 'deposito'])->get();
        $akunKasBank = Akun::where('tipe_akun', 'like', '%Kas%')
            ->orWhere('tipe_akun', 'like', '%Bank%')
            ->orWhere('nama_akun', 'like', '%Kas%')
            ->orWhere('nama_akun', 'like', '%Bank%')
            ->get();

        return view('simpanan.tarik', compact('anggotaList', 'jenisSimpanan', 'akunKasBank'));
    }

    /**
     * Kartu simpanan per anggota
     */
    public function kartu(string $id_anggota)
    {
        $anggota = Anggota::findOrFail($id_anggota);
        $simpanan = Simpanan::with('jenisSimpanan')
            ->where('id_anggota', $id_anggota)
            ->orderBy('tanggal')
            ->get();

        // Group by jenis simpanan
        $grouped = $simpanan->groupBy('id_jenis_simpanan');
        $summary = [];
        
        foreach ($grouped as $jenisId => $items) {
            $jenis = JenisSimpanan::find($jenisId);
            $saldo = 0;
            $mutations = [];
            
            foreach ($items as $item) {
                $amount = $item->jenis_transaksi === 'setor' ? $item->jumlah : -$item->jumlah;
                $saldo += $amount;
                $mutations[] = [
                    'tanggal' => $item->tanggal,
                    'jenis' => $item->jenis_transaksi,
                    'jumlah' => $item->jumlah,
                    'saldo' => $saldo,
                ];
            }

            $summary[$jenisId] = [
                'jenis' => $jenis,
                'mutations' => $mutations,
                'saldo' => $saldo,
            ];
        }

        return view('simpanan.kartu', compact('anggota', 'summary'));
    }
}
