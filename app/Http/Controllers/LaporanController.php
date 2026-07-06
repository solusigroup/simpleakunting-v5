<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\UnitUsaha;
use App\Models\JurnalDetail;
use App\Models\Persediaan;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function __construct()
    {
        \App\Models\Jurnal::$applyApprovalFilter = true;
    }

    public function index()
    {
        return view('laporan.index');
    }

    public function neraca(Request $request)
    {
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $bandingTanggal = $request->input('banding_tanggal');
        
        // Ambil filter
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));
        $projectId = $request->input('id_project');

        $perusahaan = DB::table('perusahaan')->find(1);

        // Ambil semua akun Neraca
        $akunNeraca = Akun::whereIn('tipe_akun', [
            'Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap',
            'Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang', 'Ekuitas'
        ])->orderBy('kode_akun')->get();

        // Helper untuk hitung saldo per tanggal
        $hitungSaldo = function ($tanggal) use ($akunNeraca, $cabangId, $unitId, $projectId) {
            return $akunNeraca->map(function ($akun) use ($tanggal, $cabangId, $unitId, $projectId) {
                // Clone akun agar tidak merubah referensi asli saat loop kedua
                $akunClone = clone $akun;
                
                $query = JurnalDetail::where('kode_akun', $akun->kode_akun)
                    ->whereHas('jurnal', function ($q) use ($tanggal, $cabangId, $unitId, $projectId) {
                        $q->where('tanggal', '<=', $tanggal);
                        if ($cabangId) $q->where('id_cabang', $cabangId);
                        if ($unitId) $q->where('id_unit_usaha', $unitId);
                        if ($projectId) $q->where('id_project', $projectId);
                    });

                $saldo = $query->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(kredit) as total_kredit'))
                    ->first();

                $totalDebit = $saldo->total_debit ?? 0;
                $totalKredit = $saldo->total_kredit ?? 0;

                if ($akun->saldo_normal == 'Debit') {
                    $akunClone->saldo_akhir = $akun->saldo_awal + $totalDebit - $totalKredit;
                } else {
                    $akunClone->saldo_akhir = $akun->saldo_awal + $totalKredit - $totalDebit;
                }
                return $akunClone;
            });
        };

        // Data Utama
        $laporan = $hitungSaldo($perTanggal);
        
        // Data Pembanding (jika ada)
        $laporanBanding = $bandingTanggal ? $hitungSaldo($bandingTanggal) : collect([]);

        // Grouping Data Utama
        $asetLancar = $laporan->whereIn('tipe_akun', ['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya']);
        $asetTetap = $laporan->where('tipe_akun', 'Aset Tetap');
        $kewajiban = $laporan->whereIn('tipe_akun', ['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang']);
        $ekuitas = $laporan->where('tipe_akun', 'Ekuitas');

        // Laba Rugi Berjalan
        $labaRugiBerjalan = $this->hitungLabaRugi($perTanggal, $cabangId, $unitId, $projectId);
        $labaRugiBerjalanBanding = $bandingTanggal ? $this->hitungLabaRugi($bandingTanggal, $cabangId, $unitId, $projectId) : 0;

        // Balance Check
        $totalAset = $asetLancar->sum('saldo_akhir') + $asetTetap->sum('saldo_akhir');
        $totalKewajibanEkuitas = $kewajiban->sum('saldo_akhir') + $ekuitas->sum('saldo_akhir') + $labaRugiBerjalan;
        $selisih = round($totalAset - $totalKewajibanEkuitas, 2);
        $isBalanced = abs($selisih) < 0.01; // Toleransi pembulatan

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::orderBy('nama_project')->get();

        return view('laporan.neraca', compact(
            'perusahaan', 'perTanggal', 'bandingTanggal', 
            'asetLancar', 'asetTetap', 'kewajiban', 'ekuitas', 
            'labaRugiBerjalan', 'labaRugiBerjalanBanding', 'laporanBanding',
            'totalAset', 'totalKewajibanEkuitas', 'isBalanced', 'selisih',
            'cabang', 'unitUsaha', 'projects'
        ));
    }

    public function labaRugi(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $startBanding = $request->input('start_banding');
        $endBanding = $request->input('end_banding');
        
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));
        $projectId = $request->input('id_project');

        $perusahaan = DB::table('perusahaan')->find(1);

        $akunLabaRugi = Akun::whereIn('tipe_akun', [
            'Pendapatan', 'Pendapatan Lainnya', 'HPP', 'Beban', 'Beban Lainnya'
        ])->orderBy('kode_akun')->get();

        $hitungPeriode = function ($start, $end) use ($akunLabaRugi, $cabangId, $unitId, $projectId) {
            return $akunLabaRugi->map(function ($akun) use ($start, $end, $cabangId, $unitId, $projectId) {
                $akunClone = clone $akun;
                $query = JurnalDetail::where('kode_akun', $akun->kode_akun)
                    ->whereHas('jurnal', function ($q) use ($start, $end, $cabangId, $unitId, $projectId) {
                        $q->whereBetween('tanggal', [$start, $end]);
                        if ($cabangId) $q->where('id_cabang', $cabangId);
                        if ($unitId) $q->where('id_unit_usaha', $unitId);
                        if ($projectId) $q->where('id_project', $projectId);
                    });

                $saldo = $query->select(DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(kredit) as total_kredit'))
                    ->first();

                $totalDebit = $saldo->total_debit ?? 0;
                $totalKredit = $saldo->total_kredit ?? 0;

                if ($akun->saldo_normal == 'Kredit') {
                    $akunClone->saldo_periode = $akun->saldo_awal + $totalKredit - $totalDebit;
                } else {
                    $akunClone->saldo_periode = $akun->saldo_awal + $totalDebit - $totalKredit;
                }
                return $akunClone;
            });
        };

        // Periode Utama
        $laporan = $hitungPeriode($startDate, $endDate);
        
        // Periode Pembanding
        $laporanBanding = ($startBanding && $endBanding) ? $hitungPeriode($startBanding, $endBanding) : collect([]);

        $pendapatan = $laporan->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
        $hpp = $laporan->where('tipe_akun', 'HPP');
        $beban = $laporan->whereIn('tipe_akun', ['Beban', 'Beban Lainnya']);

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::orderBy('nama_project')->get();

        return view('laporan.labarugi', compact(
            'perusahaan', 'startDate', 'endDate', 'startBanding', 'endBanding',
            'pendapatan', 'hpp', 'beban', 'laporanBanding',
            'cabang', 'unitUsaha', 'projects'
        ));
    }

    public function arusKasLangsung(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        
        $perusahaan = DB::table('perusahaan')->find(1);
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));
        $projectId = $request->input('id_project');

        // Helper untuk mendapatkan total arus kas berdasarkan tipe akun lawan
        $getFlow = function ($tipeAkunLawan, $isMasuk) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
            // Logic:
            // 1. Ambil semua ID Jurnal yang memiliki detail akun Kas & Bank dalam range tanggal
            $jurnalQuery = JurnalDetail::whereHas('akun', function($q) {
                    $q->where('tipe_akun', 'Kas & Bank');
                })
                ->whereHas('jurnal', function($q) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                    if ($cabangId) $q->where('id_cabang', $cabangId);
                    if ($unitId) $q->where('id_unit_usaha', $unitId);
                    if ($projectId) $q->where('id_project', $projectId);
                });

            $jurnalIds = $jurnalQuery->pluck('id_jurnal');

            // 2. Dari ID Jurnal tersebut, cari detail yang BUKAN Kas & Bank (Lawannya)
            // Dan tipe akun lawannya sesuai parameter
            $query = JurnalDetail::whereIn('id_jurnal', $jurnalIds)
                ->whereHas('akun', function($q) use ($tipeAkunLawan) {
                    if (is_array($tipeAkunLawan)) {
                        $q->whereIn('tipe_akun', $tipeAkunLawan);
                    } else {
                        $q->where('tipe_akun', $tipeAkunLawan);
                    }
                });

            if ($isMasuk) {
                return $query->sum('kredit');
            } else {
                return $query->sum('debit');
            }
        };

        // --- AKTIVITAS OPERASI ---
        $terimaPelanggan = $getFlow(['Piutang', 'Pendapatan', 'Pendapatan Lainnya'], true);
        $bayarPemasok = $getFlow(['Utang Usaha', 'HPP', 'Beban', 'Beban Lainnya', 'Kewajiban Lancar Lainnya', 'Persediaan', 'Aset Lancar Lainnya'], false);
        $arusKasOperasi = $terimaPelanggan - $bayarPemasok;

        // --- AKTIVITAS INVESTASI ---
        $jualAset = $getFlow('Aset Tetap', true);
        $beliAset = $getFlow('Aset Tetap', false);
        $arusKasInvestasi = $jualAset - $beliAset;

        // --- AKTIVITAS PENDANAAN ---
        $terimaPendanaan = $getFlow(['Ekuitas', 'Kewajiban Jangka Panjang'], true);
        $bayarPendanaan = $getFlow(['Ekuitas', 'Kewajiban Jangka Panjang'], false);
        $arusKasPendanaan = $terimaPendanaan - $bayarPendanaan;

        $kenaikanKas = $arusKasOperasi + $arusKasInvestasi + $arusKasPendanaan;

        // Saldo Awal Kas
        $saldoAwal = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Kas & Bank');
            })
            ->whereHas('jurnal', function($q) use ($startDate, $cabangId, $unitId, $projectId) {
                $q->where('tanggal', '<', $startDate);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })
            ->sum(DB::raw('debit - kredit'));

        $saldoAwalAkunKas = Akun::where('tipe_akun', 'Kas & Bank')->sum('saldo_awal');
        $saldoAwal += $saldoAwalAkunKas;

        $saldoAkhir = $saldoAwal + $kenaikanKas;

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::orderBy('nama_project')->get();

        return view('laporan.aruskas_langsung', compact(
            'perusahaan', 'startDate', 'endDate',
            'terimaPelanggan', 'bayarPemasok', 'arusKasOperasi',
            'jualAset', 'beliAset', 'arusKasInvestasi',
            'terimaPendanaan', 'bayarPendanaan', 'arusKasPendanaan',
            'kenaikanKas', 'saldoAwal', 'saldoAkhir',
            'cabang', 'unitUsaha', 'projects'
        ));
    }

    public function arusKasTidakLangsung(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $perusahaan = DB::table('perusahaan')->find(1);
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));
        $projectId = $request->input('id_project');

        // 1. Laba Bersih
        $pendapatan = JurnalDetail::whereHas('akun', function($q) {
            $q->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
        })->whereHas('jurnal', function ($q) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
            if ($cabangId) $q->where('id_cabang', $cabangId);
            if ($unitId) $q->where('id_unit_usaha', $unitId);
            if ($projectId) $q->where('id_project', $projectId);
        })->sum(DB::raw('kredit - debit'));

        $beban = JurnalDetail::whereHas('akun', function($q) {
            $q->whereIn('tipe_akun', ['HPP', 'Beban', 'Beban Lainnya']);
        })->whereHas('jurnal', function ($q) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
            if ($cabangId) $q->where('id_cabang', $cabangId);
            if ($unitId) $q->where('id_unit_usaha', $unitId);
            if ($projectId) $q->where('id_project', $projectId);
        })->sum(DB::raw('debit - kredit'));

        $labaBersih = $pendapatan - $beban;

        // 2. Penyesuaian Non-Kas (Penyusutan)
        $bebanPenyusutan = JurnalDetail::whereHas('akun', function($q) {
            $q->where('nama_akun', 'like', '%Penyusutan%')
              ->orWhere('nama_akun', 'like', '%Depresiasi%');
        })->whereHas('jurnal', function ($q) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
            if ($cabangId) $q->where('id_cabang', $cabangId);
            if ($unitId) $q->where('id_unit_usaha', $unitId);
            if ($projectId) $q->where('id_project', $projectId);
        })->sum('debit');

        // 3. Perubahan Modal Kerja
        $getChange = function ($tipeAkun, $saldoNormal) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
            $awal = JurnalDetail::whereHas('akun', function($q) use ($tipeAkun) {
                $q->where('tipe_akun', $tipeAkun);
            })->whereHas('jurnal', function ($q) use ($startDate, $cabangId, $unitId, $projectId) {
                $q->where('tanggal', '<', $startDate);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })->sum(DB::raw($saldoNormal == 'Debit' ? 'debit - kredit' : 'kredit - debit'));

            $akhir = JurnalDetail::whereHas('akun', function($q) use ($tipeAkun) {
                $q->where('tipe_akun', $tipeAkun);
            })->whereHas('jurnal', function ($q) use ($endDate, $cabangId, $unitId, $projectId) {
                $q->where('tanggal', '<=', $endDate);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })->sum(DB::raw($saldoNormal == 'Debit' ? 'debit - kredit' : 'kredit - debit'));

            return $akhir - $awal;
        };

        // Kenaikan Piutang (Mengurangi Kas)
        $kenaikanPiutang = $getChange('Piutang', 'Debit');
        
        // Kenaikan Persediaan (Mengurangi Kas)
        $kenaikanPersediaan = $getChange('Persediaan', 'Debit');
        
        // Kenaikan Utang Usaha (Menambah Kas)
        $kenaikanUtang = $getChange('Utang Usaha', 'Kredit');

        // Arus Kas Operasi
        $arusKasOperasi = $labaBersih + $bebanPenyusutan - $kenaikanPiutang - $kenaikanPersediaan + $kenaikanUtang;

        // --- INVESTASI & PENDANAAN ---
        $getFlowSimple = function ($tipeAkunLawan, $isMasuk) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
             $jurnalIds = JurnalDetail::whereHas('akun', function($q) {
                    $q->where('tipe_akun', 'Kas & Bank');
                })
                ->whereHas('jurnal', function($q) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
                    $q->whereBetween('tanggal', [$startDate, $endDate]);
                    if ($cabangId) $q->where('id_cabang', $cabangId);
                    if ($unitId) $q->where('id_unit_usaha', $unitId);
                    if ($projectId) $q->where('id_project', $projectId);
                })
                ->pluck('id_jurnal');

            $query = JurnalDetail::whereIn('id_jurnal', $jurnalIds)
                ->whereHas('akun', function($q) use ($tipeAkunLawan) {
                     if (is_array($tipeAkunLawan)) {
                        $q->whereIn('tipe_akun', $tipeAkunLawan);
                    } else {
                        $q->where('tipe_akun', $tipeAkunLawan);
                    }
                });

            return $isMasuk ? $query->sum('kredit') : $query->sum('debit');
        };

        $arusKasInvestasi = $getFlowSimple('Aset Tetap', true) - $getFlowSimple('Aset Tetap', false);
        $arusKasPendanaan = $getFlowSimple(['Ekuitas', 'Kewajiban Jangka Panjang'], true) - $getFlowSimple(['Ekuitas', 'Kewajiban Jangka Panjang'], false);

        $kenaikanKas = $arusKasOperasi + $arusKasInvestasi + $arusKasPendanaan;

        // Saldo Awal Kas
        $saldoAwal = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Kas & Bank');
            })
            ->whereHas('jurnal', function($q) use ($startDate, $cabangId, $unitId, $projectId) {
                $q->where('tanggal', '<', $startDate);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })
            ->sum(DB::raw('debit - kredit'));

        $saldoAwalAkunKas = Akun::where('tipe_akun', 'Kas & Bank')->sum('saldo_awal');
        $saldoAwal += $saldoAwalAkunKas;

        $saldoAkhir = $saldoAwal + $kenaikanKas;

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::orderBy('nama_project')->get();

        return view('laporan.aruskas_tidak_langsung', compact(
            'perusahaan', 'startDate', 'endDate',
            'labaBersih', 'bebanPenyusutan', 'kenaikanPiutang', 'kenaikanPersediaan', 'kenaikanUtang',
            'arusKasOperasi', 'arusKasInvestasi', 'arusKasPendanaan',
            'kenaikanKas', 'saldoAwal', 'saldoAkhir',
            'cabang', 'unitUsaha', 'projects'
        ));
    }

    public function perubahanEkuitas(Request $request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));
        $projectId = $request->input('id_project');

        $perusahaan = DB::table('perusahaan')->find(1);

        // 1. Saldo Awal Ekuitas (Sebelum Start Date)
        $saldoAwalAkunEkuitas = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Ekuitas');
            })
            ->whereHas('jurnal', function($q) use ($startDate, $cabangId, $unitId, $projectId) {
                $q->where('tanggal', '<', $startDate);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })
            ->sum(DB::raw('kredit - debit'));

        $saldoAwalAkunMaster = Akun::where('tipe_akun', 'Ekuitas')->sum('saldo_awal');
        $saldoAwalAkunEkuitas += $saldoAwalAkunMaster;

        $labaDitahanAwal = $this->hitungLabaRugi($startDate, $cabangId, $unitId, $projectId);

        $saldoAwal = $saldoAwalAkunEkuitas + $labaDitahanAwal;

        // 2. Perubahan Selama Periode
        $labaBersih = $this->hitungLabaRugiPeriode($startDate, $endDate, $cabangId, $unitId, $projectId);

        // Setoran Modal (Kredit ke Ekuitas selama periode)
        $setoranModal = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Ekuitas');
            })
            ->whereHas('jurnal', function($q) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })
            ->sum('kredit');

        // Prive / Penarikan (Debit ke Ekuitas selama periode)
        $prive = JurnalDetail::whereHas('akun', function($q) {
                $q->where('tipe_akun', 'Ekuitas');
            })
            ->whereHas('jurnal', function($q) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })
            ->sum('debit');

        $saldoAkhir = $saldoAwal + $labaBersih + $setoranModal - $prive;

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::orderBy('nama_project')->get();

        return view('laporan.perubahan_ekuitas', compact(
            'perusahaan', 'startDate', 'endDate',
            'saldoAwal', 'labaBersih', 'setoranModal', 'prive', 'saldoAkhir',
            'cabang', 'unitUsaha', 'projects'
        ));
    }

    private function hitungLabaRugiPeriode($startDate, $endDate, $cabangId = null, $unitId = null, $projectId = null)
    {
        $pendapatan = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })
            ->sum(DB::raw('kredit - debit'));

        $beban = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['HPP', 'Beban', 'Beban Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($startDate, $endDate, $cabangId, $unitId, $projectId) {
                $q->whereBetween('tanggal', [$startDate, $endDate]);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })
            ->sum(DB::raw('debit - kredit'));

        return $pendapatan - $beban;
    }

    private function hitungLabaRugi($perTanggal, $cabangId = null, $unitId = null, $projectId = null)
    {
        $pendapatan = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($perTanggal, $cabangId, $unitId, $projectId) {
                $q->where('tanggal', '<=', $perTanggal);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })
            ->sum(DB::raw('kredit - debit'));

        $beban = JurnalDetail::whereHas('akun', function($q) {
                $q->whereIn('tipe_akun', ['HPP', 'Beban', 'Beban Lainnya']);
            })
            ->whereHas('jurnal', function ($q) use ($perTanggal, $cabangId, $unitId, $projectId) {
                $q->where('tanggal', '<=', $perTanggal);
                if ($cabangId) $q->where('id_cabang', $cabangId);
                if ($unitId) $q->where('id_unit_usaha', $unitId);
                if ($projectId) $q->where('id_project', $projectId);
            })
            ->sum(DB::raw('debit - kredit'));

        return $pendapatan - $beban;
    }

    public function persediaan(Request $request)
    {
        $perusahaan = DB::table('perusahaan')->find(1);
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));

        $query = Persediaan::orderBy('nama_barang');
        if ($cabangId) $query->where('id_cabang', $cabangId);
        if ($unitId) $query->where('id_unit_usaha', $unitId);

        $persediaan = $query->get();
        
        // Hitung total nilai persediaan
        $totalNilai = $persediaan->sum(function($item) {
            return $item->stok_saat_ini * $item->harga_beli;
        });

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('laporan.persediaan', compact('perusahaan', 'persediaan', 'totalNilai', 'cabang', 'unitUsaha'));
    }

    /**
     * Laporan Mutasi Persediaan (kartu_stok history)
     */
    public function mutasiPersediaan(Request $request)
    {
        $perusahaan = DB::table('perusahaan')->first();
        $dariTanggal = $request->input('dari_tanggal', now()->startOfMonth()->format('Y-m-d'));
        $sampaiTanggal = $request->input('sampai_tanggal', now()->format('Y-m-d'));
        $idBarang = $request->input('id_barang');

        $query = DB::table('kartu_stok')
            ->join('master_persediaan', 'kartu_stok.id_barang', '=', 'master_persediaan.id_barang')
            ->whereBetween('kartu_stok.created_at', [$dariTanggal . ' 00:00:00', $sampaiTanggal . ' 23:59:59'])
            ->select(
                'kartu_stok.*',
                'master_persediaan.kode_barang',
                'master_persediaan.nama_barang'
            )
            ->orderBy('kartu_stok.created_at', 'desc');

        if ($idBarang) {
            $query->where('kartu_stok.id_barang', $idBarang);
        }

        $mutasi = $query->get();
        $barang = Persediaan::orderBy('nama_barang')->get();

        return view('laporan.mutasi-persediaan', compact('perusahaan', 'mutasi', 'barang'));
    }

    /**
     * Laporan Daftar Aset Tetap
     */
    public function daftarAsetTetap(Request $request)
    {
        $perusahaan = DB::table('perusahaan')->find(1);
        $cabangId = $request->input('id_cabang', session('active_cabang'));

        $query = \App\Models\FixedAsset::with('group')->orderBy('tanggal_perolehan', 'asc');
        
        if ($cabangId) {
            $query->where('cabang_id', $cabangId);
        }

        $status = $request->input('status');
        if ($status) {
            $query->where('status', $status);
        }

        $asets = $query->get();

        // Rekapitulasi per kelompok
        $rekapGroup = [];
        foreach ($asets as $aset) {
            $groupName = $aset->group->nama_kelompok ?? 'Tanpa Kelompok';
            if (!isset($rekapGroup[$groupName])) {
                $rekapGroup[$groupName] = [
                    'jumlah_aset' => 0,
                    'total_perolehan' => 0,
                    'total_akumulasi' => 0,
                    'total_nilai_buku' => 0,
                ];
            }
            $rekapGroup[$groupName]['jumlah_aset'] += 1;
            $rekapGroup[$groupName]['total_perolehan'] += $aset->harga_perolehan;
            $rekapGroup[$groupName]['total_akumulasi'] += ($aset->harga_perolehan - $aset->nilai_buku_saat_ini);
            $rekapGroup[$groupName]['total_nilai_buku'] += $aset->nilai_buku_saat_ini;
        }

        $cabang = Cabang::orderBy('nama_cabang')->get();

        return view('laporan.aset_tetap', compact('perusahaan', 'asets', 'rekapGroup', 'cabang', 'status'));
    }

    /**
     * Export Neraca to PDF
     */
    public function neracaPdf(Request $request)
    {
        $report = \App\Models\ReportDownload::create([
            'nama_laporan' => 'Neraca',
            'tipe' => 'pdf',
            'params' => json_encode($request->all()),
            'status' => 'pending',
            'created_by' => auth()->id()
        ]);

        \App\Jobs\GeneratePdfReportJob::dispatch($report->id);

        return redirect()->route('laporan.downloads')->with('success', 'Laporan Neraca sedang digenerate. Silakan cek halaman unduhan secara berkala.');
    }

    /**
     * Export Laba Rugi to PDF
     */
    public function labaRugiPdf(Request $request)
    {
        $report = \App\Models\ReportDownload::create([
            'nama_laporan' => 'Laba Rugi',
            'tipe' => 'pdf',
            'params' => json_encode($request->all()),
            'status' => 'pending',
            'created_by' => auth()->id()
        ]);

        \App\Jobs\GeneratePdfReportJob::dispatch($report->id);

        return redirect()->route('laporan.downloads')->with('success', 'Laporan Laba Rugi sedang digenerate. Silakan cek halaman unduhan secara berkala.');
    }

    // =====================================================
    // LAPORAN SIMPAN PINJAM
    // =====================================================

    /**
     * Outstanding Simpanan dan Pinjaman
     */
    public function outstandingSimpanPinjam(Request $request)
    {
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $perusahaan = DB::table('perusahaan')->find(1);
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));

        // Outstanding Simpanan per Anggota
        $simpananQuery = DB::table('simpanan')
            ->join('anggota', 'simpanan.id_anggota', '=', 'anggota.id_anggota')
            ->join('jenis_simpanan', 'simpanan.id_jenis_simpanan', '=', 'jenis_simpanan.id_jenis_simpanan')
            ->where('simpanan.tanggal', '<=', $perTanggal);
            
        if ($cabangId) $simpananQuery->where('simpanan.id_cabang', $cabangId);
        if ($unitId) $simpananQuery->where('simpanan.id_unit_usaha', $unitId);

        $simpanan = $simpananQuery->select(
                'anggota.id_anggota',
                'anggota.nama_lengkap',
                'anggota.no_anggota',
                'jenis_simpanan.nama_simpanan',
                DB::raw("SUM(CASE WHEN simpanan.jenis_transaksi = 'setor' THEN simpanan.jumlah ELSE -simpanan.jumlah END) as saldo")
            )
            ->groupBy('anggota.id_anggota', 'anggota.nama_lengkap', 'anggota.no_anggota', 'jenis_simpanan.nama_simpanan')
            ->having('saldo', '>', 0)
            ->orderBy('anggota.nama_lengkap')
            ->get();

        // Outstanding Pinjaman per Anggota
        $pinjamanQuery = DB::table('pinjaman')
            ->join('anggota', 'pinjaman.id_anggota', '=', 'anggota.id_anggota')
            ->join('jenis_pinjaman', 'pinjaman.id_jenis_pinjaman', '=', 'jenis_pinjaman.id_jenis_pinjaman')
            ->where('pinjaman.tanggal_pengajuan', '<=', $perTanggal)
            ->whereIn('pinjaman.status', ['active', 'disbursed']);
            
        if ($cabangId) $pinjamanQuery->where('pinjaman.id_cabang', $cabangId);
        if ($unitId) $pinjamanQuery->where('pinjaman.id_unit_usaha', $unitId);

        $pinjaman = $pinjamanQuery->select(
                'anggota.id_anggota',
                'anggota.nama_lengkap',
                'anggota.no_anggota',
                'jenis_pinjaman.nama_pinjaman',
                'pinjaman.jumlah_pinjaman',
                'pinjaman.sisa_pokok',
                'pinjaman.tanggal_jatuh_tempo'
            )
            ->orderBy('anggota.nama_lengkap')
            ->get();

        $totalSimpanan = $simpanan->sum('saldo');
        $totalPinjaman = $pinjaman->sum('sisa_pokok');

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('laporan.outstanding_simpan_pinjam', compact(
            'perusahaan', 'perTanggal', 'simpanan', 'pinjaman', 'totalSimpanan', 'totalPinjaman',
            'cabang', 'unitUsaha'
        ));
    }

    /**
     * Kolektibilitas Pinjaman
     */
    public function kolektibilitasPinjaman(Request $request)
    {
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $perusahaan = DB::table('perusahaan')->find(1);

        // Klasifikasi Kolektibilitas OJK:
        // 1 = Lancar (0-30 hari)
        // 2 = Dalam Perhatian Khusus (31-90 hari)
        // 3 = Kurang Lancar (91-120 hari)
        // 4 = Diragukan (121-180 hari)
        // 5 = Macet (>180 hari)

        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));

        $pinjamanQuery = DB::table('pinjaman')
            ->join('anggota', 'pinjaman.id_anggota', '=', 'anggota.id_anggota')
            ->join('jenis_pinjaman', 'pinjaman.id_jenis_pinjaman', '=', 'jenis_pinjaman.id_jenis_pinjaman')
            ->whereIn('pinjaman.status', ['active', 'disbursed']);
            
        if ($cabangId) $pinjamanQuery->where('pinjaman.id_cabang', $cabangId);
        if ($unitId) $pinjamanQuery->where('pinjaman.id_unit_usaha', $unitId);

        $pinjaman = $pinjamanQuery->select(
                'pinjaman.*',
                'anggota.nama_lengkap',
                'anggota.no_anggota',
                'jenis_pinjaman.nama_pinjaman'
            )
            ->get()
            ->map(function ($item) use ($perTanggal) {
                // Hitung hari tunggakan
                $jatuhTempo = $item->tanggal_jatuh_tempo ?? $item->tanggal_pengajuan;
                $hariTunggak = max(0, (strtotime($perTanggal) - strtotime($jatuhTempo)) / 86400);

                // Tentukan kolektibilitas
                if ($hariTunggak <= 30) {
                    $item->kolektibilitas = 1;
                    $item->status_kolektibilitas = 'Lancar';
                } elseif ($hariTunggak <= 90) {
                    $item->kolektibilitas = 2;
                    $item->status_kolektibilitas = 'Dalam Perhatian Khusus';
                } elseif ($hariTunggak <= 180) {
                    $item->kolektibilitas = 4;
                    $item->status_kolektibilitas = 'Diragukan';
                } else {
                    $item->kolektibilitas = 5;
                    $item->status_kolektibilitas = 'Macet';
                }

                $item->hari_tunggak = $hariTunggak;
                return $item;
            });

        // Rekapitulasi per Kolektibilitas
        $rekap = $pinjaman->groupBy('kolektibilitas')->map(function ($group, $kol) {
            return [
                'kolektibilitas' => $kol,
                'status' => $group->first()->status_kolektibilitas,
                'jumlah_pinjaman' => $group->count(),
                'total_sisa_pokok' => $group->sum('sisa_pokok'),
            ];
        })->sortBy('kolektibilitas')->values();

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('laporan.kolektibilitas_pinjaman', compact(
            'perusahaan', 'perTanggal', 'pinjaman', 'rekap', 'cabang', 'unitUsaha'
        ));
    }

    /**
     * Perhitungan dan Pembagian SHU
     */
    public function perhitunganShu(Request $request)
    {
        $tahun = $request->input('tahun', date('Y'));
        $perusahaan = DB::table('perusahaan')->find(1);

        $startDate = $tahun . '-01-01';
        $endDate = $tahun . '-12-31';

        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));

        // 1. Hitung Pendapatan Koperasi
        $pendapatanBungaQuery = DB::table('pinjaman_angsuran')
            ->whereBetween('tanggal_bayar', [$startDate, $endDate]);
            
        if ($cabangId) $pendapatanBungaQuery->where('id_cabang', $cabangId);
        if ($unitId) $pendapatanBungaQuery->where('id_unit_usaha', $unitId);
        
        $pendapatanBunga = $pendapatanBungaQuery->sum('bunga_dibayar');

        $pendapatanAdminQuery = DB::table('pinjaman')
            ->whereBetween('tanggal_pengajuan', [$startDate, $endDate]);
            
        if ($cabangId) $pendapatanAdminQuery->where('id_cabang', $cabangId);
        if ($unitId) $pendapatanAdminQuery->where('id_unit_usaha', $unitId);
        
        $pendapatanAdmin = $pendapatanAdminQuery->sum('biaya_admin');

        $pendapatanLain = 0; // Bisa ditambahkan sumber lain

        $totalPendapatan = $pendapatanBunga + $pendapatanAdmin + $pendapatanLain;

        // 2. Hitung Beban Koperasi (dari jurnal)
        $totalBeban = JurnalDetail::whereHas('akun', function($q) {
            $q->whereIn('tipe_akun', ['Beban', 'Beban Lainnya']);
        })->whereHas('jurnal', function ($q) use ($startDate, $endDate, $cabangId, $unitId) {
            $q->whereBetween('tanggal', [$startDate, $endDate]);
            if ($cabangId) $q->where('id_cabang', $cabangId);
            if ($unitId) $q->where('id_unit_usaha', $unitId);
        })->sum(DB::raw('debit - kredit'));

        // 3. SHU Bersih
        $shuBersih = $totalPendapatan - $totalBeban;

        // 4. Pembagian SHU (contoh: 40% untuk anggota, 60% untuk koperasi)
        $persenAnggota = 40;
        $shuAnggota = $shuBersih * ($persenAnggota / 100);

        // 5. Hitung kontribusi per anggota (berdasarkan simpanan dan pinjaman)
        $anggota = DB::table('anggota')
            ->where('status', 'aktif')
            ->get()
            ->map(function ($a) use ($startDate, $endDate, $shuAnggota) {
                // Total simpanan anggota
                $simpanan = DB::table('simpanan')
                    ->where('id_anggota', $a->id_anggota)
                    ->whereBetween('tanggal', [$startDate, $endDate])
                    ->where('jenis_transaksi', 'setor')
                    ->sum('jumlah');

                // Total jasa pinjaman anggota
                $jasaPinjaman = DB::table('pinjaman_angsuran')
                    ->join('pinjaman', 'pinjaman_angsuran.id_pinjaman', '=', 'pinjaman.id_pinjaman')
                    ->where('pinjaman.id_anggota', $a->id_anggota)
                    ->whereBetween('pinjaman_angsuran.tanggal_bayar', [$startDate, $endDate])
                    ->sum('pinjaman_angsuran.bunga_dibayar');

                $a->simpanan = $simpanan;
                $a->jasa_pinjaman = $jasaPinjaman;
                $a->kontribusi = $simpanan + $jasaPinjaman;
                return $a;
            });

        $totalKontribusi = $anggota->sum('kontribusi');

        // Hitung SHU per anggota
        $anggota = $anggota->map(function ($a) use ($shuAnggota, $totalKontribusi) {
            $a->shu = $totalKontribusi > 0 ? ($a->kontribusi / $totalKontribusi) * $shuAnggota : 0;
            return $a;
        })->filter(function ($a) {
            return $a->kontribusi > 0;
        });

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('laporan.perhitungan_shu', compact(
            'perusahaan', 'tahun', 'pendapatanBunga', 'pendapatanAdmin', 'pendapatanLain',
            'totalPendapatan', 'totalBeban', 'shuBersih', 'persenAnggota', 'shuAnggota',
            'anggota', 'totalKontribusi', 'cabang', 'unitUsaha'
        ));
    }

    public function neracaLajur(Request $request)
    {
        $endDate = $request->input('end_date', date('Y-m-d'));
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));

        $perusahaan = DB::table('perusahaan')->find(1);
        $akun = Akun::orderBy('kode_akun')->get();

        $data = $akun->map(function ($a) use ($endDate, $cabangId, $unitId) {
            // 1. Neraca Saldo (Trial Balance) - Kolom 1-2
            $nsQuery = JurnalDetail::where('kode_akun', $a->kode_akun)
                ->whereHas('jurnal', function ($q) use ($endDate, $cabangId, $unitId) {
                    $q->where('tanggal', '<=', $endDate)
                      ->where('sumber_jurnal', '!=', 'Penyesuaian')
                      ->where('sumber_jurnal', '!=', 'Closing');
                    if ($cabangId) $q->where('id_cabang', $cabangId);
                    if ($unitId) $q->where('id_unit_usaha', $unitId);
                });
            
            $nsRes = $nsQuery->select(DB::raw('SUM(debit) as d'), DB::raw('SUM(kredit) as k'))->first();
            $ns_d_raw = $nsRes->d ?? 0;
            $ns_k_raw = $nsRes->k ?? 0;

            if ($a->saldo_normal == 'Debit') {
                $ns_d_raw += $a->saldo_awal;
            } else {
                $ns_k_raw += $a->saldo_awal;
            }

            $ns_d = 0; $ns_k = 0;
            if ($a->saldo_normal == 'Debit') {
                $val = $ns_d_raw - $ns_k_raw;
                if ($val >= 0) $ns_d = $val; else $ns_k = abs($val);
            } else {
                $val = $ns_k_raw - $ns_d_raw;
                if ($val >= 0) $ns_k = $val; else $ns_d = abs($val);
            }

            // 2. Penyesuaian (Kolom 3-4)
            $adjQuery = JurnalDetail::where('kode_akun', $a->kode_akun)
                ->whereHas('jurnal', function ($q) use ($endDate, $cabangId, $unitId) {
                    $q->where('tanggal', '<=', $endDate)
                      ->where('sumber_jurnal', '=', 'Penyesuaian');
                    if ($cabangId) $q->where('id_cabang', $cabangId);
                    if ($unitId) $q->where('id_unit_usaha', $unitId);
                });
            
            $adjRes = $adjQuery->select(DB::raw('SUM(debit) as d'), DB::raw('SUM(kredit) as k'))->first();
            $adj_d = $adjRes->d ?? 0;
            $adj_k = $adjRes->k ?? 0;

            // 3. NS Setelah Penyesuaian (Kolom 5-6)
            $total_d_raw = $ns_d_raw + $adj_d;
            $total_k_raw = $ns_k_raw + $adj_k;
            
            $nssp_d = 0; $nssp_k = 0;
            if ($a->saldo_normal == 'Debit') {
                $val = $total_d_raw - $total_k_raw;
                if ($val >= 0) $nssp_d = $val; else $nssp_k = abs($val);
            } else {
                $val = $total_k_raw - $total_d_raw;
                if ($val >= 0) $nssp_k = $val; else $nssp_d = abs($val);
            }

            // 4. Laba Rugi (Kolom 7-8)
            $lr_d = 0; $lr_k = 0;
            if (in_array($a->tipe_akun, ['Pendapatan', 'Pendapatan Lainnya', 'HPP', 'Beban', 'Beban Lainnya'])) {
                $lr_d = $nssp_d;
                $lr_k = $nssp_k;
            }

            // 5. Neraca (Kolom 9-10)
            $n_d = 0; $n_k = 0;
            if (in_array($a->tipe_akun, ['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap', 'Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang', 'Ekuitas'])) {
                $n_d = $nssp_d;
                $n_k = $nssp_k;
            }

            return [
                'kode_akun' => $a->kode_akun,
                'nama_akun' => $a->nama_akun,
                'ns_d' => $ns_d,
                'ns_k' => $ns_k,
                'adj_d' => $adj_d,
                'adj_k' => $adj_k,
                'nssp_d' => $nssp_d,
                'nssp_k' => $nssp_k,
                'lr_d' => $lr_d,
                'lr_k' => $lr_k,
                'n_d' => $n_d,
                'n_k' => $n_k,
            ];
        });

        $data = $data->filter(function($item) {
            return (round($item['ns_d'], 2) != 0 || round($item['ns_k'], 2) != 0 || 
                    round($item['adj_d'], 2) != 0 || round($item['adj_k'], 2) != 0 ||
                    round($item['nssp_d'], 2) != 0 || round($item['nssp_k'], 2) != 0);
        });

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('laporan.neraca_lajur', compact('perusahaan', 'endDate', 'data', 'cabang', 'unitUsaha'));
    }

    public function checkNeraca(Request $request)
    {
        try {
            $perTanggal = $request->input('per_tanggal', date('Y-m-d'));

            // 1. Check Unbalanced Journals
            $unbalancedJournals = DB::table('jurnal_detail')
                ->select('id_jurnal', DB::raw('SUM(debit) as total_debit'), DB::raw('SUM(kredit) as total_kredit'))
                ->groupBy('id_jurnal')
                ->havingRaw('ABS(SUM(debit) - SUM(kredit)) > 0.01')
                ->get();
            
            $unbalancedData = [];
            if ($unbalancedJournals->count() > 0) {
                $unbalancedData = \App\Models\Jurnal::whereIn('id_jurnal', $unbalancedJournals->pluck('id_jurnal'))
                    ->get()
                    ->map(function($j) use ($unbalancedJournals) {
                        $stats = $unbalancedJournals->firstWhere('id_jurnal', $j->id_jurnal);
                        $j->total_debit = $stats->total_debit;
                        $j->total_kredit = $stats->total_kredit;
                        $j->selisih = abs($stats->total_debit - $stats->total_kredit);
                        return $j;
                    });
            }

            // 2. Check Accounts with Invalid Types
            $allowedTypes = [
                'Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap',
                'Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang', 'Ekuitas',
                'Pendapatan', 'Pendapatan Lainnya', 'HPP', 'Beban', 'Beban Lainnya'
            ];

            $invalidAccounts = Akun::all()->filter(function($a) use ($allowedTypes) {
                return empty($a->tipe_akun) || !in_array(trim($a->tipe_akun), $allowedTypes);
            });

            // 3. Equation Breakdown (Gap Analysis)
            $totalAset = $this->sumByTypesAudit(['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap'], $perTanggal);
            $totalKewajiban = $this->sumByTypesAudit(['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang'], $perTanggal);
            $totalEkuitas = $this->sumByTypesAudit(['Ekuitas'], $perTanggal);
            $labaBerjalan = $this->hitungLabaRugi($perTanggal);

            $totalPasiva = $totalKewajiban + $totalEkuitas + $labaBerjalan;
            $gap = $totalAset - $totalPasiva;

            // 4. Orphaned Details
            $orphanedDetails = DB::table('jurnal_detail')
                ->leftJoin('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
                ->whereNull('jurnal_umum.id_jurnal')
                ->count();

            // 5. Missing Master Accounts
            $usedAccounts = DB::table('jurnal_detail')->distinct()->pluck('kode_akun');
            $masterAccounts = Akun::pluck('kode_akun')->toArray();
            $missingMasterAccounts = collect($usedAccounts)->diff($masterAccounts);

            // 6. Grand Integrity Check (Total Debit vs Total Kredit in whole DB)
            $grandTotals = DB::table('jurnal_detail')
                ->select(DB::raw('SUM(debit) as td'), DB::raw('SUM(kredit) as tk'))
                ->first();
            
            $grandDiff = abs(($grandTotals->td ?? 0) - ($grandTotals->tk ?? 0));
            $accountBalances = collect([]); // Placeholder to avoid 500

            // 7. Duplicate Account Names (same nama_akun, different kode_akun)
            $allAccounts = Akun::all();
            $usedKodeAkuns = DB::table('jurnal_detail')->distinct()->pluck('kode_akun')->toArray();
            $duplicateAccounts = $allAccounts->groupBy(function ($item) {
                return strtolower(trim($item->nama_akun));
            })->filter(function ($group) {
                return $group->count() > 1;
            })->map(function ($group) use ($usedKodeAkuns) {
                return $group->map(function ($akun) use ($usedKodeAkuns) {
                    $akun->has_transactions = in_array($akun->kode_akun, $usedKodeAkuns);
                    return $akun;
                });
            });

            return view('audit.neraca', compact(
                'perTanggal', 'unbalancedData', 'invalidAccounts', 'allowedTypes',
                'totalAset', 'totalKewajiban', 'totalEkuitas', 'labaBerjalan', 
                'totalPasiva', 'gap', 'orphanedDetails', 'missingMasterAccounts',
                'accountBalances', 'grandDiff', 'duplicateAccounts'
            ));
        } catch (\Exception $e) {
            return "Audit Error: " . $e->getMessage();
        }
    }

    public function bukuPembantuPiutang(Request $request)
    {
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $idPelanggan = $request->input('id_pelanggan');
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));

        $pelanggans = Pelanggan::orderBy('nama_pelanggan')->get();
        
        $data = [];
        if ($idPelanggan) {
            $selected = $pelanggans->firstWhere('id_pelanggan', $idPelanggan);
            if (!$selected) {
                $idPelanggan = null;
            } else {
                $data = JurnalDetail::whereHas('jurnal', function($q) use ($idPelanggan, $perTanggal, $cabangId, $unitId) {
                    $q->where('id_pelanggan', $idPelanggan)
                      ->where('tanggal', '<=', $perTanggal);
                    if ($cabangId) $q->where('id_cabang', $cabangId);
                    if ($unitId) $q->where('id_unit_usaha', $unitId);
                })
                ->whereHas('akun', function($q) {
                    $q->where('tipe_akun', 'Piutang');
                })
                ->with('jurnal')
                ->get()
                ->sortBy('jurnal.tanggal');
            }
        }

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('laporan.piutang', compact('pelanggans', 'data', 'perTanggal', 'idPelanggan', 'cabang', 'unitUsaha'));
    }

    public function bukuPembantuUtang(Request $request)
    {
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $idPemasok = $request->input('id_pemasok');
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));

        $pemasoks = Pemasok::orderBy('nama_pemasok')->get();
        
        $data = [];
        if ($idPemasok) {
            $selected = $pemasoks->firstWhere('id_pemasok', $idPemasok);
            if (!$selected) {
                $idPemasok = null;
            } else {
                $data = JurnalDetail::whereHas('jurnal', function($q) use ($idPemasok, $perTanggal, $cabangId, $unitId) {
                    $q->where('id_pemasok', $idPemasok)
                      ->where('tanggal', '<=', $perTanggal);
                    if ($cabangId) $q->where('id_cabang', $cabangId);
                    if ($unitId) $q->where('id_unit_usaha', $unitId);
                })
                ->whereHas('akun', function($q) {
                    $q->where('tipe_akun', 'Utang Usaha');
                })
                ->with('jurnal')
                ->get()
                ->sortBy('jurnal.tanggal');
            }
        }

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();

        return view('laporan.utang', compact('pemasoks', 'data', 'perTanggal', 'idPemasok', 'cabang', 'unitUsaha'));
    }

    private function sumByTypesAudit($types, $perTanggal)
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
                $total += $akun->saldo_awal + (($saldo->d ?? 0) - ($saldo->k ?? 0));
            } else {
                $total += $akun->saldo_awal + (($saldo->k ?? 0) - ($saldo->d ?? 0));
            }
        }
        return $total;
    }

    /**
     * Show report downloads history
     */
    public function downloads()
    {
        $perusahaan = DB::table('perusahaan')->find(1);
        $downloads = \App\Models\ReportDownload::orderBy('created_at', 'desc')->paginate(15);
        return view('laporan.downloads', compact('perusahaan', 'downloads'));
    }

    /**
     * Download specific report file
     */
    public function downloadFile($id)
    {
        $report = \App\Models\ReportDownload::findOrFail($id);
        if ($report->status !== 'completed' || !$report->file_path) {
            return back()->with('error', 'File belum tersedia atau gagal diproses.');
        }

        if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($report->file_path)) {
            return back()->with('error', 'File fisik tidak ditemukan.');
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->download($report->file_path);
    }
}
