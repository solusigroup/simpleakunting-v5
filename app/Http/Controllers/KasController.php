<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\Cabang;
use App\Models\UnitUsaha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KasController extends Controller
{
    use \App\Traits\CheckSaldoTrait;
    use \App\Traits\CheckClosedPeriod;

    public function index()
    {
        // Ambil semua akun Kas & Bank
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();

        // Hitung saldo terkini untuk setiap akun kas
        foreach ($akunKas as $akun) {
            $debit = JurnalDetail::where('kode_akun', $akun->kode_akun)->sum('debit');
            $kredit = JurnalDetail::where('kode_akun', $akun->kode_akun)->sum('kredit');
            $akun->saldo_terkini = $debit - $kredit;
        }

        return view('kas.index', compact('akunKas'));
    }

    public function transfer()
    {
        $akunKas = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();
        
        // Generate No Transaksi (TF-xxxx)
        $lastTrans = Jurnal::where('sumber_jurnal', 'Transfer Kas')->orderBy('id_jurnal', 'desc')->first();
        $nextNo = 1;
        if ($lastTrans && preg_match('/TF-(\d+)/', $lastTrans->no_transaksi, $matches)) {
            $nextNo = (int)$matches[1] + 1;
        }
        $noTransaksi = 'TF-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('kas.transfer', compact('akunKas', 'noTransaksi', 'cabang', 'unitUsaha'));
    }

    public function storeTransfer(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date|before_or_equal:today',
            'id_cabang' => 'required|exists:cabang,id',
            'id_unit_usaha' => 'required|exists:unit_usaha,id',
            'dari_akun' => 'required|exists:akun,kode_akun',
            'ke_akun' => 'required|exists:akun,kode_akun|different:dari_akun',
            'jumlah' => 'required|numeric|min:1',
            'keterangan' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            // C3: Cek periode tutup buku
            $this->validatePeriodOpen($request->tanggal);

            // H5: Generate no_transaksi atomik
            $lastTrans = Jurnal::where('sumber_jurnal', 'Transfer Kas')->orderBy('id_jurnal', 'desc')->lockForUpdate()->first();
            $nextNo = 1;
            if ($lastTrans && preg_match('/TF-(\d+)/', $lastTrans->no_transaksi, $matches)) {
                $nextNo = (int)$matches[1] + 1;
            }
            $noTransaksi = 'TF-' . str_pad($nextNo, 5, '0', STR_PAD_LEFT);

            // Cek Saldo Akun Asal
            if (!$this->checkSaldoCukup($request->dari_akun, $request->jumlah)) {
                $saldo = $this->getSaldoSaatIni($request->dari_akun);
                throw new \Exception("Saldo akun asal tidak mencukupi! Saldo saat ini: Rp " . number_format($saldo, 2, ',', '.'));
            }

            // 1. Buat Jurnal Header
            $jurnal = Jurnal::create([
                'no_transaksi' => $noTransaksi,
                'tanggal' => $request->tanggal,
                'id_cabang' => $request->id_cabang,
                'id_unit_usaha' => $request->id_unit_usaha,
                'deskripsi' => $request->keterangan,
                'sumber_jurnal' => 'Transfer Kas',
                'is_locked' => 0
            ]);

            // 2. Kredit Akun Asal (Keluar)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->dari_akun,
                'debit' => 0,
                'kredit' => $request->jumlah
            ]);

            // 3. Debit Akun Tujuan (Masuk)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $request->ke_akun,
                'debit' => $request->jumlah,
                'kredit' => 0
            ]);

            DB::commit();
            return redirect()->route('kas.index')->with('success', 'Transfer kas berhasil dilakukan.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Kas transfer error', ['error' => $e->getMessage()]);
            $msg = str_contains($e->getMessage(), 'Periode') || str_contains($e->getMessage(), 'Saldo')
                ? $e->getMessage()
                : 'Gagal melakukan transfer. Silakan coba lagi.';
            return back()->with('error', $msg)->withInput();
        }
    }
}
