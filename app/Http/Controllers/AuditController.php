<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function checkNeraca(Request $request)
    {
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));

        // 1. Check Unbalanced Journals
        $unbalancedJournals = DB::table('jurnal_detail')
            ->select('id_jurnal', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(kredit) as total_kredit'))
            ->groupBy('id_jurnal')
            ->havingRaw('ABS(SUM(debit) - SUM(kredit)) > 0.01')
            ->get();
        
        $unbalancedData = [];
        if ($unbalancedJournals->count() > 0) {
            $unbalancedData = Jurnal::whereIn('id_jurnal', $unbalancedJournals->pluck('id_jurnal'))
                ->get()
                ->map(function($j) use ($unbalancedJournals) {
                    $stats = $unbalancedJournals->firstWhere('id_jurnal', $j->id_jurnal);
                    $j->total_debit = $stats->total_debit;
                    $j->total_kredit = $stats->total_kredit;
                    $j->selisih = abs($stats->total_debit - $stats->total_kredit);
                    return $j;
                });
        }

        // 2. Check Accounts with Invalid Types (Trimming spaces for robustness)
        $allowedTypes = [
            'Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap',
            'Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang', 'Ekuitas',
            'Pendapatan', 'Pendapatan Lainnya', 'HPP', 'Beban', 'Beban Lainnya'
        ];

        $invalidAccounts = Akun::where(function($q) use ($allowedTypes) {
            $q->whereNull('tipe_akun')
              ->orWhereRaw('TRIM(tipe_akun) NOT IN ("' . implode('","', $allowedTypes) . '")');
        })->get();

        // 3. Equation Breakdown (Gap Analysis)
        $totalAset = $this->sumByTypes(['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap'], $perTanggal);
        $totalKewajiban = $this->sumByTypes(['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang'], $perTanggal);
        $totalEkuitas = $this->sumByTypes(['Ekuitas'], $perTanggal);
        $labaBerjalan = $this->hitungLabaRugi($perTanggal);

        $totalPasiva = $totalKewajiban + $totalEkuitas + $labaBerjalan;
        $gap = $totalAset - $totalPasiva;

        // 4. Orphaned Details
        $orphanedDetails = DB::table('jurnal_detail')
            ->leftJoin('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->whereNull('jurnal_umum.id_jurnal')
            ->count();

        return view('audit.neraca', compact(
            'perTanggal', 'unbalancedData', 'invalidAccounts', 'allowedTypes',
            'totalAset', 'totalKewajiban', 'totalEkuitas', 'labaBerjalan', 
            'totalPasiva', 'gap', 'orphanedDetails'
        ));
    }

    private function sumByTypes($types, $perTanggal)
    {
        $akuns = Akun::whereIn('tipe_akun', $types)->get();
        $total = 0;
        foreach ($akuns as $akun) {
            $saldo = JurnalDetail::where('kode_akun', $akun->kode_akun)
                ->whereHas('jurnal', function($q) use ($perTanggal) {
                    $q->where('tanggal', '<=', $perTanggal);
                })
                ->select(DB::raw('SUM(debit) as d'), DB::raw('SUM(kredit) as k'))
                ->first();
            
            if ($akun->saldo_normal == 'Debit') {
                $total += (($saldo->d ?? 0) - ($saldo->k ?? 0));
            } else {
                $total += (($saldo->k ?? 0) - ($saldo->d ?? 0));
            }
        }
        return $total;
    }

    private function hitungLabaRugi($perTanggal)
    {
        $pendapatan = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($perTanggal) {
                $q->where('tanggal', '<=', $perTanggal);
            })
            ->sum(DB::raw('kredit - debit'));

        $beban = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['HPP', 'Beban', 'Beban Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($perTanggal) {
                $q->where('tanggal', '<=', $perTanggal);
            })
            ->sum(DB::raw('debit - kredit'));

        return $pendapatan - $beban;
    }
}
