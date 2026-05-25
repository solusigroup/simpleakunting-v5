<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Pelanggan;
use App\Models\UnitUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    use \App\Traits\CheckClosedPeriod;
    public function index()
    {
        $penerimaan = Jurnal::with(['cabang', 'unitUsaha', 'details'])
            ->where('sumber_jurnal', 'Penerimaan Kas')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('penerimaan.index', compact('penerimaan'));
    }

    public function create(Request $request)
    {
        // Akun Kas/Bank untuk Debit
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();
        
        // Akun Pendapatan/Piutang untuk Kredit
        $akunPendapatan = Akun::whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya', 'Piutang'])->orderBy('kode_akun')->get();
        
        $pelanggan = Pelanggan::orderBy('nama_pelanggan')->get();

        // Generate No Transaksi (CR-xxxx)
        $lastTrans = Jurnal::where('sumber_jurnal', 'Penerimaan Kas')->orderBy('id_jurnal', 'desc')->first();
        $nextNo = 1;
        if ($lastTrans && preg_match('/CR-(\d+)/', $lastTrans->no_transaksi, $matches)) {
            $nextNo = (int)$matches[1] + 1;
        }
        $noTransaksi = 'CR-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::orderBy('nama_unit')->get();

        return view('penerimaan.create', compact('akunKas', 'akunPendapatan', 'pelanggan', 'noTransaksi', 'cabang', 'unitUsaha'));
    }

    public function store(Request $request)
    {
        // Hapus baris detail yang kosong (tidak dipilih akunnya dan nominal 0)
        if ($request->has('details') && is_array($request->details)) {
            $filteredDetails = array_filter($request->details, function ($detail) {
                return !empty($detail['kode_akun']) || (!empty($detail['jumlah']) && $detail['jumlah'] > 0);
            });
            $request->merge(['details' => array_values($filteredDetails)]);
        }

        $request->validate([
            'no_transaksi' => 'required|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date|before_or_equal:today',
            'akun_kas' => 'required|exists:akun,kode_akun', // Debit
            'id_pelanggan' => 'nullable|exists:pelanggan,id_pelanggan',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'nullable|exists:unit_usaha,id',
            'keterangan' => 'required|string',
            'details' => 'required|array|min:1',
            'details.*.kode_akun' => 'required|exists:akun,kode_akun', // Kredit
            'details.*.jumlah' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($request->tanggal);

            // H5: Generate no_transaksi atomik
            $lastTrans = Jurnal::where('sumber_jurnal', 'Penerimaan Kas')->orderBy('id_jurnal', 'desc')->lockForUpdate()->first();
            $nextNo = 1;
            if ($lastTrans && preg_match('/CR-(\d+)/', $lastTrans->no_transaksi, $matches)) {
                $nextNo = (int)$matches[1] + 1;
            }
            $noTransaksi = 'CR-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            $totalTerima = collect($request->details)->sum('jumlah');

            // 1. Buat Jurnal Header
            $jurnal = Jurnal::create([
                'no_transaksi' => $noTransaksi,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->keterangan,
                'id_pelanggan' => $request->id_pelanggan,
                'sumber_jurnal' => 'Penerimaan Kas',
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'is_locked' => 0
            ]);

            // 2. Jurnal Detail: Debit Kas/Bank
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->akun_kas,
                'debit' => $totalTerima,
                'kredit' => 0
            ]);

            // 3. Jurnal Detail: Kredit Akun Lawan (Pendapatan/Piutang)
            foreach ($request->details as $detail) {
                if ($detail['jumlah'] > 0) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $detail['kode_akun'],
                        'debit' => 0,
                        'kredit' => $detail['jumlah']
                    ]);

                    // Jika akun Piutang dan ada Pelanggan, update saldo piutang pelanggan
                    $akun = Akun::where('kode_akun', $detail['kode_akun'])->first();
                    if ($akun && $akun->tipe_akun == 'Piutang' && $request->id_pelanggan) {
                        $pelanggan = Pelanggan::find($request->id_pelanggan);
                        $pelanggan->saldo_terkini_piutang -= $detail['jumlah'];
                        $pelanggan->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('penerimaan.index')->with('success', 'Penerimaan kas berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            $msg = str_contains($e->getMessage(), 'Periode')
                ? $e->getMessage()
                : 'Gagal menyimpan transaksi. Silakan coba lagi.';
            return back()->with('error', $msg)->withInput();
        }
    }

    public function show($id)
    {
        $jurnal = Jurnal::with(['details.akun', 'cabang', 'unitUsaha'])->findOrFail($id);
        return view('penerimaan.show', compact('jurnal'));
    }
}
