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
        try {
            $test = DB::table('akun')->first();
            return "Reachability Test Success. Found first account: " . ($test->nama_akun ?? 'N/A');
        } catch (\Exception $e) {
            return "Reachability Test Failed: " . $e->getMessage();
        }
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
