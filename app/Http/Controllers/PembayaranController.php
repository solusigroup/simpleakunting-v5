<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Pemasok;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembayaranController extends Controller
{
    use \App\Traits\CheckSaldoTrait;
    use \App\Traits\CheckClosedPeriod;

    public function index()
    {
        $pembayaran = Jurnal::with(['cabang', 'unitUsaha', 'details'])
            ->where('sumber_jurnal', 'Pengeluaran Kas')
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('pembayaran.index', compact('pembayaran'));
    }

    public function create()
    {
        // Akun Kas/Bank untuk Kredit
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();
        
        // Akun Beban/Utang untuk Debit
        $akunBeban = Akun::whereIn('tipe_akun', ['Beban', 'Beban Lainnya', 'Utang Usaha', 'Kewajiban Lancar Lainnya'])->orderBy('kode_akun')->get();
        
        $pemasok = Pemasok::orderBy('nama_pemasok')->get();

        // Generate No Transaksi (CD-xxxx)
        $lastTrans = Jurnal::where('sumber_jurnal', 'Pengeluaran Kas')->orderBy('id_jurnal', 'desc')->first();
        $nextNo = 1;
        if ($lastTrans && preg_match('/CD-(\d+)/', $lastTrans->no_transaksi, $matches)) {
            $nextNo = (int)$matches[1] + 1;
        }
        $noTransaksi = 'CD-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        return view('pembayaran.create', compact('akunKas', 'akunBeban', 'pemasok', 'noTransaksi'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date|before_or_equal:today',
            'akun_kas' => 'required|exists:akun,kode_akun', // Kredit
            'id_pemasok' => 'nullable|exists:pemasok,id_pemasok',
            'keterangan' => 'required|string',
            'details' => 'required|array|min:1',
            'details.*.kode_akun' => 'required|exists:akun,kode_akun', // Debit
            'details.*.jumlah' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($request->tanggal);

            // H5: Generate no_transaksi atomik
            $lastTrans = Jurnal::where('sumber_jurnal', 'Pengeluaran Kas')->orderBy('id_jurnal', 'desc')->lockForUpdate()->first();
            $nextNo = 1;
            if ($lastTrans && preg_match('/CD-(\d+)/', $lastTrans->no_transaksi, $matches)) {
                $nextNo = (int)$matches[1] + 1;
            }
            $noTransaksi = 'CD-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            $totalBayar = collect($request->details)->sum('jumlah');

            // Cek Saldo
            if (!$this->checkSaldoCukup($request->akun_kas, $totalBayar)) {
                $saldo = $this->getSaldoSaatIni($request->akun_kas);
                throw new \Exception("Saldo tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo, 2, ',', '.'));
            }

            // 1. Buat Jurnal Header
            $jurnal = Jurnal::create([
                'no_transaksi' => $noTransaksi,
                'tanggal' => $request->tanggal,
                'deskripsi' => $request->keterangan,
                'id_pemasok' => $request->id_pemasok,
                'sumber_jurnal' => 'Pengeluaran Kas',
                'is_locked' => 0
            ]);

            // 2. Jurnal Detail: Kredit Kas/Bank
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->akun_kas,
                'debit' => 0,
                'kredit' => $totalBayar
            ]);

            // 3. Jurnal Detail: Debit Akun Lawan (Beban/Utang)
            foreach ($request->details as $detail) {
                if ($detail['jumlah'] > 0) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $detail['kode_akun'],
                        'debit' => $detail['jumlah'],
                        'kredit' => 0
                    ]);

                    // Jika akun Utang dan ada Pemasok, update saldo utang pemasok
                    $akun = Akun::where('kode_akun', $detail['kode_akun'])->first();
                    if ($akun && $akun->tipe_akun == 'Utang Usaha' && $request->id_pemasok) {
                        $pemasok = Pemasok::find($request->id_pemasok);
                        $pemasok->saldo_terkini_hutang -= $detail['jumlah'];
                        $pemasok->save();
                    }
                }
            }

            DB::commit();
            return redirect()->route('pembayaran.index')->with('success', 'Pengeluaran kas berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            $msg = str_contains($e->getMessage(), 'Periode') || str_contains($e->getMessage(), 'Saldo')
                ? $e->getMessage()
                : 'Gagal menyimpan transaksi. Silakan coba lagi.';
            return back()->with('error', $msg)->withInput();
        }
    }

    public function show($id)
    {
        $jurnal = Jurnal::with(['details.akun', 'cabang', 'unitUsaha'])->findOrFail($id);
        return view('pembayaran.show', compact('jurnal'));
    }
}
