<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;

class BukuBesarController extends Controller
{
    public function index(Request $request)
    {
        $akunList = Akun::orderBy('kode_akun')->get();
        
        $kodeAkun = $request->input('kode_akun');
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));

        $transaksi = collect([]);
        $saldoAwal = 0;
        $selectedAkun = null;

        if ($kodeAkun) {
            $selectedAkun = Akun::find($kodeAkun);
            
            // Hitung Saldo Awal (Transaksi sebelum start_date)
            // Note: Ini simplifikasi. Idealnya ada tabel saldo_awal_periode atau hitung dari awal tahun.
            // Untuk sekarang kita hitung semua transaksi sebelum start_date.
            
            $sums = JurnalDetail::where('kode_akun', $kodeAkun)
                ->whereHas('jurnal', function ($q) use ($startDate) {
                    $q->where('tanggal', '<', $startDate);
                })
                ->selectRaw('SUM(debit) as total_debit, SUM(kredit) as total_kredit')
                ->first();

            $debitAwal = $sums->total_debit ?? 0;
            $kreditAwal = $sums->total_kredit ?? 0;

            if ($selectedAkun->saldo_normal == 'Debit') {
                $saldoAwal = $selectedAkun->saldo_awal + $debitAwal - $kreditAwal;
            } else {
                $saldoAwal = $selectedAkun->saldo_awal + $kreditAwal - $debitAwal;
            }

            // Ambil Transaksi Periode Ini
            $transaksi = JurnalDetail::with('jurnal')
                ->join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
                ->where('jurnal_detail.kode_akun', $kodeAkun)
                ->whereBetween('jurnal_umum.tanggal', [$startDate, $endDate])
                ->orderBy('jurnal_umum.tanggal', 'asc')
                ->orderBy('jurnal_umum.id_jurnal', 'asc')
                ->select('jurnal_detail.*')
                ->get();
        }

        return view('bukubesar.index', compact('akunList', 'transaksi', 'saldoAwal', 'selectedAkun', 'startDate', 'endDate', 'kodeAkun'));
    }
}
