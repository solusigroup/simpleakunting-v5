<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\Cabang;
use App\Models\UnitUsaha;
use App\Models\Project;
use App\Models\JurnalDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalisaController extends Controller
{
    public function __construct()
    {
        \App\Models\Jurnal::$applyApprovalFilter = true;
    }

    /**
     * 1. Sub Menu: Analisa Laporan Keuangan
     * - Analisa Vertikal (Common Size)
     * - Analisa Horizontal (Pertumbuhan / Trend YoY / MoM)
     */
    public function laporanKeuangan(Request $request)
    {
        $perusahaan = DB::table('perusahaan')->find(1);
        
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $bandingTanggal = $request->input('banding_tanggal', date('Y-m-d', strtotime('-1 year', strtotime($perTanggal))));
        
        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));
        $projectId = $request->input('id_project');

        // 1. Data Neraca Periode Berjalan & Pembanding
        $neracaUtama = $this->getNeracaData($perTanggal, $cabangId, $unitId, $projectId);
        $neracaBanding = $this->getNeracaData($bandingTanggal, $cabangId, $unitId, $projectId);

        // Gabungkan Neraca & Hitung Vertikal + Horizontal
        $totalAsetUtama = $neracaUtama['totalAset'];
        $totalAsetBanding = $neracaBanding['totalAset'];

        $analisaNeraca = $neracaUtama['akun']->map(function ($item) use ($neracaBanding, $totalAsetUtama, $totalAsetBanding) {
            $itemBanding = $neracaBanding['akun']->firstWhere('kode_akun', $item->kode_akun);
            $saldoBanding = $itemBanding ? $itemBanding->saldo_akhir : 0;
            
            $nominalPerubahan = $item->saldo_akhir - $saldoBanding;
            $persenPerubahan = ($saldoBanding != 0) ? (($nominalPerubahan / abs($saldoBanding)) * 100) : (($item->saldo_akhir != 0) ? 100 : 0);
            
            $commonSizeUtama = ($totalAsetUtama > 0) ? (($item->saldo_akhir / $totalAsetUtama) * 100) : 0;
            $commonSizeBanding = ($totalAsetBanding > 0) ? (($saldoBanding / $totalAsetBanding) * 100) : 0;

            return (object) [
                'kode_akun' => $item->kode_akun,
                'nama_akun' => $item->nama_akun,
                'tipe_akun' => $item->tipe_akun,
                'saldo_normal' => $item->saldo_normal,
                'saldo_utama' => $item->saldo_akhir,
                'saldo_banding' => $saldoBanding,
                'nominal_perubahan' => $nominalPerubahan,
                'persen_perubahan' => $persenPerubahan,
                'common_size_utama' => $commonSizeUtama,
                'common_size_banding' => $commonSizeBanding,
            ];
        });

        // 2. Data Laba Rugi Periode Berjalan (Tahun Berjalan) & Pembanding
        $startUtama = date('Y-01-01', strtotime($perTanggal));
        $startBanding = date('Y-01-01', strtotime($bandingTanggal));

        $lrUtama = $this->getLabaRugiData($startUtama, $perTanggal, $cabangId, $unitId, $projectId);
        $lrBanding = $this->getLabaRugiData($startBanding, $bandingTanggal, $cabangId, $unitId, $projectId);

        $totalPendapatanUtama = $lrUtama['totalPendapatan'];
        $totalPendapatanBanding = $lrBanding['totalPendapatan'];

        $analisaLabaRugi = $lrUtama['akun']->map(function ($item) use ($lrBanding, $totalPendapatanUtama, $totalPendapatanBanding) {
            $itemBanding = $lrBanding['akun']->firstWhere('kode_akun', $item->kode_akun);
            $saldoBanding = $itemBanding ? $itemBanding->saldo_periode : 0;

            $nominalPerubahan = $item->saldo_periode - $saldoBanding;
            $persenPerubahan = ($saldoBanding != 0) ? (($nominalPerubahan / abs($saldoBanding)) * 100) : (($item->saldo_periode != 0) ? 100 : 0);

            $commonSizeUtama = ($totalPendapatanUtama > 0) ? (($item->saldo_periode / $totalPendapatanUtama) * 100) : 0;
            $commonSizeBanding = ($totalPendapatanBanding > 0) ? (($saldoBanding / $totalPendapatanBanding) * 100) : 0;

            return (object) [
                'kode_akun' => $item->kode_akun,
                'nama_akun' => $item->nama_akun,
                'tipe_akun' => $item->tipe_akun,
                'saldo_normal' => $item->saldo_normal,
                'saldo_utama' => $item->saldo_periode,
                'saldo_banding' => $saldoBanding,
                'nominal_perubahan' => $nominalPerubahan,
                'persen_perubahan' => $persenPerubahan,
                'common_size_utama' => $commonSizeUtama,
                'common_size_banding' => $commonSizeBanding,
            ];
        });

        // Summary Pertumbuhan Utama
        $growth = [
            'pendapatan' => [
                'utama' => $lrUtama['totalPendapatan'],
                'banding' => $lrBanding['totalPendapatan'],
                'persen' => ($lrBanding['totalPendapatan'] > 0) ? (($lrUtama['totalPendapatan'] - $lrBanding['totalPendapatan']) / $lrBanding['totalPendapatan']) * 100 : 0
            ],
            'laba_bersih' => [
                'utama' => $lrUtama['labaBersih'],
                'banding' => $lrBanding['labaBersih'],
                'persen' => ($lrBanding['labaBersih'] != 0) ? (($lrUtama['labaBersih'] - $lrBanding['labaBersih']) / abs($lrBanding['labaBersih'])) * 100 : 0
            ],
            'total_aset' => [
                'utama' => $totalAsetUtama,
                'banding' => $totalAsetBanding,
                'persen' => ($totalAsetBanding > 0) ? (($totalAsetUtama - $totalAsetBanding) / $totalAsetBanding) * 100 : 0
            ],
            'total_ekuitas' => [
                'utama' => $neracaUtama['totalEkuitas'] + $lrUtama['labaBersih'],
                'banding' => $neracaBanding['totalEkuitas'] + $lrBanding['labaBersih'],
                'persen' => (($neracaBanding['totalEkuitas'] + $lrBanding['labaBersih']) != 0) ? ((($neracaUtama['totalEkuitas'] + $lrUtama['labaBersih']) - ($neracaBanding['totalEkuitas'] + $lrBanding['labaBersih'])) / abs($neracaBanding['totalEkuitas'] + $lrBanding['labaBersih'])) * 100 : 0
            ]
        ];

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::orderBy('nama_project')->get();

        return view('analisa.laporan_keuangan', compact(
            'perusahaan', 'perTanggal', 'bandingTanggal',
            'analisaNeraca', 'totalAsetUtama', 'totalAsetBanding',
            'analisaLabaRugi', 'totalPendapatanUtama', 'totalPendapatanBanding',
            'lrUtama', 'lrBanding', 'growth',
            'cabang', 'unitUsaha', 'projects'
        ));
    }

    /**
     * 2. Sub Menu: Rasio-Rasio Keuangan Rekomendasi
     */
    public function rasio(Request $request)
    {
        $perusahaan = DB::table('perusahaan')->find(1);
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $startDate = $request->input('start_date', date('Y-01-01', strtotime($perTanggal)));

        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));
        $projectId = $request->input('id_project');

        $metrics = $this->calculateFinancialMetrics($startDate, $perTanggal, $cabangId, $unitId, $projectId);
        $ratios = $this->computeRatioCategories($metrics);

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::orderBy('nama_project')->get();

        return view('analisa.rasio', compact(
            'perusahaan', 'perTanggal', 'startDate',
            'metrics', 'ratios',
            'cabang', 'unitUsaha', 'projects'
        ));
    }

    /**
     * 3. Sub Menu: Kesehatan Perusahaan (Altman Z-Score & Health Index)
     */
    public function kesehatanPerusahaan(Request $request)
    {
        $perusahaan = DB::table('perusahaan')->find(1);
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $startDate = $request->input('start_date', date('Y-01-01', strtotime($perTanggal)));

        $cabangId = $request->input('id_cabang', session('active_cabang'));
        $unitId = $request->input('id_unit_usaha', session('active_unit'));
        $projectId = $request->input('id_project');

        $metrics = $this->calculateFinancialMetrics($startDate, $perTanggal, $cabangId, $unitId, $projectId);
        $healthAnalysis = $this->evaluateCompanyHealth($metrics);

        $cabang = Cabang::orderBy('nama_cabang')->get();
        $unitUsaha = UnitUsaha::active()->orderBy('nama_unit')->get();
        $projects = Project::orderBy('nama_project')->get();

        return view('analisa.kesehatan_perusahaan', compact(
            'perusahaan', 'perTanggal', 'startDate',
            'metrics', 'healthAnalysis',
            'cabang', 'unitUsaha', 'projects'
        ));
    }

    /**
     * Helper: Ekstrak Data Neraca per Tanggal
     */
    private function getNeracaData($tanggal, $cabangId = null, $unitId = null, $projectId = null)
    {
        $akunNeraca = Akun::whereIn('tipe_akun', [
            'Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap',
            'Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang', 'Ekuitas'
        ])->orderBy('kode_akun')->get();

        $aggregated = JurnalDetail::join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_umum.tanggal', '<=', $tanggal)
            ->when($cabangId, fn($q) => $q->where('jurnal_umum.id_cabang', $cabangId))
            ->when($unitId, fn($q) => $q->where('jurnal_umum.id_unit_usaha', $unitId))
            ->when($projectId, fn($q) => $q->where('jurnal_umum.id_project', $projectId))
            ->groupBy('jurnal_detail.kode_akun')
            ->select(
                'jurnal_detail.kode_akun',
                DB::raw('SUM(jurnal_detail.debit) as total_debit'),
                DB::raw('SUM(jurnal_detail.kredit) as total_kredit')
            )
            ->get()
            ->keyBy('kode_akun');

        $mapped = $akunNeraca->map(function ($akun) use ($aggregated) {
            $akunClone = clone $akun;
            $row = $aggregated->get($akun->kode_akun);
            $totalDebit = $row ? $row->total_debit : 0;
            $totalKredit = $row ? $row->total_kredit : 0;

            if ($akun->saldo_normal == 'Debit') {
                $akunClone->saldo_akhir = $akun->saldo_awal + $totalDebit - $totalKredit;
            } else {
                $akunClone->saldo_akhir = $akun->saldo_awal + $totalKredit - $totalDebit;
            }
            return $akunClone;
        });

        $asetLancar = $mapped->whereIn('tipe_akun', ['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya'])->sum('saldo_akhir');
        $asetTetap = $mapped->where('tipe_akun', 'Aset Tetap')->sum('saldo_akhir');
        $kewajibanLancar = $mapped->whereIn('tipe_akun', ['Utang Usaha', 'Kewajiban Lancar Lainnya'])->sum('saldo_akhir');
        $kewajibanPanjang = $mapped->where('tipe_akun', 'Kewajiban Jangka Panjang')->sum('saldo_akhir');
        $ekuitas = $mapped->where('tipe_akun', 'Ekuitas')->sum('saldo_akhir');

        return [
            'akun' => $mapped,
            'asetLancar' => $asetLancar,
            'asetTetap' => $asetTetap,
            'totalAset' => $asetLancar + $asetTetap,
            'kewajibanLancar' => $kewajibanLancar,
            'kewajibanPanjang' => $kewajibanPanjang,
            'totalKewajiban' => $kewajibanLancar + $kewajibanPanjang,
            'totalEkuitas' => $ekuitas,
        ];
    }

    /**
     * Helper: Ekstrak Data Laba Rugi Range Tanggal
     */
    private function getLabaRugiData($startDate, $endDate, $cabangId = null, $unitId = null, $projectId = null)
    {
        $akunLabaRugi = Akun::whereIn('tipe_akun', [
            'Pendapatan', 'Pendapatan Lainnya', 'HPP', 'Beban', 'Beban Lainnya'
        ])->orderBy('kode_akun')->get();

        $aggregated = JurnalDetail::join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->whereBetween('jurnal_umum.tanggal', [$startDate, $endDate])
            ->when($cabangId, fn($q) => $q->where('jurnal_umum.id_cabang', $cabangId))
            ->when($unitId, fn($q) => $q->where('jurnal_umum.id_unit_usaha', $unitId))
            ->when($projectId, fn($q) => $q->where('jurnal_umum.id_project', $projectId))
            ->groupBy('jurnal_detail.kode_akun')
            ->select(
                'jurnal_detail.kode_akun',
                DB::raw('SUM(jurnal_detail.debit) as total_debit'),
                DB::raw('SUM(jurnal_detail.kredit) as total_kredit')
            )
            ->get()
            ->keyBy('kode_akun');

        $mapped = $akunLabaRugi->map(function ($akun) use ($aggregated) {
            $akunClone = clone $akun;
            $row = $aggregated->get($akun->kode_akun);
            $totalDebit = $row ? $row->total_debit : 0;
            $totalKredit = $row ? $row->total_kredit : 0;

            if ($akun->saldo_normal == 'Kredit') {
                $akunClone->saldo_periode = $akun->saldo_awal + $totalKredit - $totalDebit;
            } else {
                $akunClone->saldo_periode = $akun->saldo_awal + $totalDebit - $totalKredit;
            }
            return $akunClone;
        });

        $pendapatanUtama = $mapped->where('tipe_akun', 'Pendapatan')->sum('saldo_periode');
        $pendapatanLainnya = $mapped->where('tipe_akun', 'Pendapatan Lainnya')->sum('saldo_periode');
        $hpp = $mapped->where('tipe_akun', 'HPP')->sum('saldo_periode');
        $bebanOperasional = $mapped->where('tipe_akun', 'Beban')->sum('saldo_periode');
        $bebanLainnya = $mapped->where('tipe_akun', 'Beban Lainnya')->sum('saldo_periode');

        $totalPendapatan = $pendapatanUtama + $pendapatanLainnya;
        $labaKotor = $pendapatanUtama - $hpp;
        $labaOperasional = $labaKotor - $bebanOperasional;
        $labaBersih = $totalPendapatan - ($hpp + $bebanOperasional + $bebanLainnya);

        return [
            'akun' => $mapped,
            'pendapatanUtama' => $pendapatanUtama,
            'pendapatanLainnya' => $pendapatanLainnya,
            'totalPendapatan' => $totalPendapatan,
            'hpp' => $hpp,
            'labaKotor' => $labaKotor,
            'bebanOperasional' => $bebanOperasional,
            'bebanLainnya' => $bebanLainnya,
            'totalBeban' => $bebanOperasional + $bebanLainnya,
            'labaOperasional' => $labaOperasional,
            'labaBersih' => $labaBersih,
        ];
    }

    /**
     * Helper: Kalkulasi Metrik Finansial Gabungan
     */
    private function calculateFinancialMetrics($startDate, $endDate, $cabangId = null, $unitId = null, $projectId = null)
    {
        $neraca = $this->getNeracaData($endDate, $cabangId, $unitId, $projectId);
        $lr = $this->getLabaRugiData($startDate, $endDate, $cabangId, $unitId, $projectId);

        $kasBank = $neraca['akun']->where('tipe_akun', 'Kas & Bank')->sum('saldo_akhir');
        $piutang = $neraca['akun']->where('tipe_akun', 'Piutang')->sum('saldo_akhir');
        $persediaan = $neraca['akun']->where('tipe_akun', 'Persediaan')->sum('saldo_akhir');

        $modalKerja = $neraca['asetLancar'] - $neraca['kewajibanLancar'];
        $totalEkuitasTerkini = $neraca['totalEkuitas'] + $lr['labaBersih'];

        return [
            'kas_bank' => $kasBank,
            'piutang' => $piutang,
            'persediaan' => $persediaan,
            'aset_lancar' => $neraca['asetLancar'],
            'aset_tetap' => $neraca['asetTetap'],
            'total_aset' => $neraca['totalAset'],
            'kewajiban_lancar' => $neraca['kewajibanLancar'],
            'kewajiban_panjang' => $neraca['kewajibanPanjang'],
            'total_kewajiban' => $neraca['totalKewajiban'],
            'total_ekuitas' => $totalEkuitasTerkini,
            'modal_kerja' => $modalKerja,
            'pendapatan' => $lr['totalPendapatan'],
            'hpp' => $lr['hpp'],
            'laba_kotor' => $lr['labaKotor'],
            'laba_operasional' => $lr['labaOperasional'],
            'laba_bersih' => $lr['labaBersih'],
        ];
    }

    /**
     * Helper: Hitung Rasio Finansial Esensial
     */
    private function computeRatioCategories($m)
    {
        // 1. Likuiditas
        $currentRatio = ($m['kewajiban_lancar'] > 0) ? ($m['aset_lancar'] / $m['kewajiban_lancar']) : ($m['aset_lancar'] > 0 ? 99 : 0);
        $quickRatio = ($m['kewajiban_lancar'] > 0) ? (($m['aset_lancar'] - $m['persediaan']) / $m['kewajiban_lancar']) : (($m['aset_lancar'] - $m['persediaan']) > 0 ? 99 : 0);
        $cashRatio = ($m['kewajiban_lancar'] > 0) ? ($m['kas_bank'] / $m['kewajiban_lancar']) : ($m['kas_bank'] > 0 ? 99 : 0);

        // 2. Profitabilitas
        $gpm = ($m['pendapatan'] > 0) ? (($m['laba_kotor'] / $m['pendapatan']) * 100) : 0;
        $npm = ($m['pendapatan'] > 0) ? (($m['laba_bersih'] / $m['pendapatan']) * 100) : 0;
        $roa = ($m['total_aset'] > 0) ? (($m['laba_bersih'] / $m['total_aset']) * 100) : 0;
        $roe = ($m['total_ekuitas'] > 0) ? (($m['laba_bersih'] / $m['total_ekuitas']) * 100) : 0;

        // 3. Solvabilitas
        $dar = ($m['total_aset'] > 0) ? (($m['total_kewajiban'] / $m['total_aset']) * 100) : 0;
        $der = ($m['total_ekuitas'] > 0) ? (($m['total_kewajiban'] / $m['total_ekuitas']) * 100) : 0;

        // 4. Aktivitas
        $tato = ($m['total_aset'] > 0) ? ($m['pendapatan'] / $m['total_aset']) : 0;
        $inventoryTurnover = ($m['persediaan'] > 0) ? ($m['hpp'] / $m['persediaan']) : 0;

        return [
            'likuiditas' => [
                'current_ratio' => [
                    'nama' => 'Current Ratio',
                    'nilai' => $currentRatio,
                    'format' => number_format($currentRatio, 2, ',', '.') . 'x',
                    'benchmark' => '≥ 1.50x - 2.00x',
                    'status' => $currentRatio >= 1.5 ? 'success' : ($currentRatio >= 1.0 ? 'warning' : 'danger'),
                    'status_label' => $currentRatio >= 1.5 ? 'Sehat / Likuid' : ($currentRatio >= 1.0 ? 'Cukup / Waspada' : 'Rentan / Kurang Likuid'),
                    'deskripsi' => 'Kemampuan aset lancar dalam menjamin pembayaran seluruh kewajiban jangka pendek.'
                ],
                'quick_ratio' => [
                    'nama' => 'Quick Ratio (Acid-Test)',
                    'nilai' => $quickRatio,
                    'format' => number_format($quickRatio, 2, ',', '.') . 'x',
                    'benchmark' => '≥ 1.00x',
                    'status' => $quickRatio >= 1.0 ? 'success' : ($quickRatio >= 0.7 ? 'warning' : 'danger'),
                    'status_label' => $quickRatio >= 1.0 ? 'Sangat Aman' : ($quickRatio >= 0.7 ? 'Cukup' : 'Kurang Likuid'),
                    'deskripsi' => 'Kemampuan membayar kewajiban lancar menggunakan aset yang paling likuid (tanpa mengandalkan penjualan persediaan).'
                ],
                'cash_ratio' => [
                    'nama' => 'Cash Ratio',
                    'nilai' => $cashRatio,
                    'format' => number_format($cashRatio, 2, ',', '.') . 'x',
                    'benchmark' => '≥ 0.30x - 0.50x',
                    'status' => $cashRatio >= 0.3 ? 'success' : ($cashRatio >= 0.1 ? 'warning' : 'danger'),
                    'status_label' => $cashRatio >= 0.3 ? 'Kas Kuat' : ($cashRatio >= 0.1 ? 'Kas Sedang' : 'Kas Tipis'),
                    'deskripsi' => 'Proporsi ketersediaan kas & setara kas murni terhadap utang jangka pendek.'
                ],
            ],
            'profitabilitas' => [
                'gpm' => [
                    'nama' => 'Gross Profit Margin (GPM)',
                    'nilai' => $gpm,
                    'format' => number_format($gpm, 2, ',', '.') . '%',
                    'benchmark' => '≥ 20.00%',
                    'status' => $gpm >= 20 ? 'success' : ($gpm >= 10 ? 'warning' : 'danger'),
                    'status_label' => $gpm >= 20 ? 'Margin Tebal' : ($gpm >= 10 ? 'Moderat' : 'Margin Tipis'),
                    'deskripsi' => 'Persentase laba kotor dari total pendapatan setelah memperhitungkan Harga Pokok Penjualan (HPP).'
                ],
                'npm' => [
                    'nama' => 'Net Profit Margin (NPM)',
                    'nilai' => $npm,
                    'format' => number_format($npm, 2, ',', '.') . '%',
                    'benchmark' => '≥ 5.00% - 10.00%',
                    'status' => $npm >= 10 ? 'success' : ($npm >= 5 ? 'warning' : 'danger'),
                    'status_label' => $npm >= 10 ? 'Sangat Menguntungkan' : ($npm >= 5 ? 'Cukup' : 'Rendah / Rugi'),
                    'deskripsi' => 'Keuntungan bersih aktual per rupiah pendapatan yang berhasil diperoleh perusahaan.'
                ],
                'roa' => [
                    'nama' => 'Return on Assets (ROA)',
                    'nilai' => $roa,
                    'format' => number_format($roa, 2, ',', '.') . '%',
                    'benchmark' => '≥ 5.00%',
                    'status' => $roa >= 5.0 ? 'success' : ($roa >= 2.0 ? 'warning' : 'danger'),
                    'status_label' => $roa >= 5.0 ? 'Efisiensi Aset Tinggi' : ($roa >= 2.0 ? 'Moderat' : 'Kurang Efisien'),
                    'deskripsi' => 'Efektivitas manajemen dalam mendayagunakan seluruh aset perusahaan untuk menghasilkan laba bersih.'
                ],
                'roe' => [
                    'nama' => 'Return on Equity (ROE)',
                    'nilai' => $roe,
                    'format' => number_format($roe, 2, ',', '.') . '%',
                    'benchmark' => '≥ 10.00%',
                    'status' => $roe >= 12.0 ? 'success' : ($roe >= 6.0 ? 'warning' : 'danger'),
                    'status_label' => $roe >= 12.0 ? 'Tingkat Pengembalian Prima' : ($roe >= 6.0 ? 'Cukup' : 'Rendah'),
                    'deskripsi' => 'Imbal hasil atas modal sendiri yang diinvestasikan pemilik/pemegang saham.'
                ],
            ],
            'solvabilitas' => [
                'dar' => [
                    'nama' => 'Debt to Asset Ratio (DAR)',
                    'nilai' => $dar,
                    'format' => number_format($dar, 2, ',', '.') . '%',
                    'benchmark' => '≤ 50.00%',
                    'status' => $dar <= 50 ? 'success' : ($dar <= 70 ? 'warning' : 'danger'),
                    'status_label' => $dar <= 50 ? 'Solvabel / Aman' : ($dar <= 70 ? 'Cukup Berisiko' : 'Risiko Utang Tinggi'),
                    'deskripsi' => 'Persentase aset perusahaan yang didanai oleh pinjaman/utang.'
                ],
                'der' => [
                    'nama' => 'Debt to Equity Ratio (DER)',
                    'nilai' => $der,
                    'format' => number_format($der, 2, ',', '.') . '%',
                    'benchmark' => '≤ 100.00%',
                    'status' => $der <= 100 ? 'success' : ($der <= 200 ? 'warning' : 'danger'),
                    'status_label' => $der <= 100 ? 'Struktur Modal Sehat' : ($der <= 200 ? 'Leverage Tinggi' : 'Ketergantungan Utang Kritis'),
                    'deskripsi' => 'Rasio perbandingan antara total utang dengan total modal sendiri.'
                ],
            ],
            'aktivitas' => [
                'tato' => [
                    'nama' => 'Total Asset Turnover (TATO)',
                    'nilai' => $tato,
                    'format' => number_format($tato, 2, ',', '.') . 'x / thn',
                    'benchmark' => '≥ 1.00x',
                    'status' => $tato >= 1.0 ? 'success' : ($tato >= 0.5 ? 'warning' : 'danger'),
                    'status_label' => $tato >= 1.0 ? 'Perputaran Cepat' : ($tato >= 0.5 ? 'Cukup Aktif' : 'Aset Lambat'),
                    'deskripsi' => 'Kecepatan perputaran seluruh aset perusahaan dalam menghasilkan volume penjualan.'
                ],
                'inventory_turnover' => [
                    'nama' => 'Perputaran Persediaan (ITO)',
                    'nilai' => $inventoryTurnover,
                    'format' => number_format($inventoryTurnover, 2, ',', '.') . 'x',
                    'benchmark' => '≥ 4.00x',
                    'status' => $inventoryTurnover >= 4.0 ? 'success' : ($inventoryTurnover >= 2.0 ? 'warning' : 'danger'),
                    'status_label' => $inventoryTurnover >= 4.0 ? 'Perputaran Stok Prima' : ($inventoryTurnover >= 2.0 ? 'Normal' : 'Potensi Dead Stock'),
                    'deskripsi' => 'Frekuensi barang persediaan terjual dan digantikan dalam suatu periode.'
                ],
            ]
        ];
    }

    /**
     * 3. Helper: Evaluasi Kesehatan Perusahaan Komprehensif & Altman Z-Score
     */
    private function evaluateCompanyHealth($m)
    {
        $totalAset = $m['total_aset'] > 0 ? $m['total_aset'] : 1;
        $totalKewajiban = $m['total_kewajiban'] > 0 ? $m['total_kewajiban'] : 1;

        // Altman Z'-Score untuk Perusahaan Swasta / Non-Publik (Modified Model)
        // Z' = 0.717(X1) + 0.847(X2) + 3.107(X3) + 0.420(X4) + 0.998(X5)
        $x1 = $m['modal_kerja'] / $totalAset;                   // Modal Kerja / Total Aset
        $x2 = ($m['laba_bersih']) / $totalAset;                 // Laba Ditahan / Total Aset
        $x3 = $m['laba_operasional'] / $totalAset;              // EBIT / Total Aset
        $x4 = $m['total_ekuitas'] / $totalKewajiban;            // Nilai Buku Ekuitas / Total Utang
        $x5 = $m['pendapatan'] / $totalAset;                    // Penjualan / Total Aset

        $zScore = (0.717 * $x1) + (0.847 * $x2) + (3.107 * $x3) + (0.420 * $x4) + (0.998 * $x5);

        // Kategori Z-Score (Private Firm Model):
        // Z' > 2.90 = Safe Zone (Sehat)
        // 1.23 <= Z' <= 2.90 = Grey Zone (Waspada)
        // Z' < 1.23 = Distress Zone (Bahaya)
        if ($zScore >= 2.90) {
            $zZone = 'safe';
            $zLabel = 'Zona Aman (Safe Zone)';
            $zColor = 'success';
            $zDesc = 'Perusahaan berada dalam kondisi keuangan yang sangat solid, stabil, dan minim risiko gagal bayar / kebangkrutan.';
        } elseif ($zScore >= 1.23) {
            $zZone = 'grey';
            $zLabel = 'Zona Waspada (Grey Zone)';
            $zColor = 'warning';
            $zDesc = 'Kondisi keuangan perusahaan cukup stabil namun terdapat kerentanan pada modal kerja, profitabilitas, atau struktur utang.';
        } else {
            $zZone = 'distress';
            $zLabel = 'Zona Bahaya (Distress Zone)';
            $zColor = 'danger';
            $zDesc = 'Perusahaan menghadapi tekanan likuiditas dan beban kewajiban yang signifikan. Diperlukan tindakan restrukturisasi segera.';
        }

        // Skor Kesehatan Finansial 4-Pilar (Skala 0 - 100)
        // 1. Skor Likuiditas (Maks 25)
        $currentRatio = ($m['kewajiban_lancar'] > 0) ? ($m['aset_lancar'] / $m['kewajiban_lancar']) : 2.0;
        $scoreLikuiditas = min(25, max(0, ($currentRatio / 2.0) * 25));

        // 2. Skor Profitabilitas (Maks 35)
        $npm = ($m['pendapatan'] > 0) ? (($m['laba_bersih'] / $m['pendapatan']) * 100) : 0;
        $roa = ($m['total_aset'] > 0) ? (($m['laba_bersih'] / $m['total_aset']) * 100) : 0;
        $npmScore = min(20, max(0, ($npm / 15.0) * 20));
        $roaScore = min(15, max(0, ($roa / 10.0) * 15));
        $scoreProfitabilitas = $npmScore + $roaScore;

        // 3. Skor Solvabilitas (Maks 25)
        $dar = ($m['total_aset'] > 0) ? (($m['total_kewajiban'] / $m['total_aset']) * 100) : 0;
        $scoreSolvabilitas = ($dar <= 50) ? 25 : max(0, 25 - (($dar - 50) * 0.5));

        // 4. Skor Efisiensi & Aktivitas (Maks 15)
        $tato = ($m['total_aset'] > 0) ? ($m['pendapatan'] / $m['total_aset']) : 0;
        $scoreEfisiensi = min(15, max(0, ($tato / 1.5) * 15));

        $totalScore = round($scoreLikuiditas + $scoreProfitabilitas + $scoreSolvabilitas + $scoreEfisiensi);

        if ($totalScore >= 80) {
            $grade = 'AAA';
            $gradeLabel = 'Sangat Sehat (Prima)';
            $gradeBadge = 'success';
        } elseif ($totalScore >= 65) {
            $grade = 'AA';
            $gradeLabel = 'Sehat';
            $gradeBadge = 'info';
        } elseif ($totalScore >= 50) {
            $grade = 'BBB';
            $gradeLabel = 'Cukup Sehat (Moderat)';
            $gradeBadge = 'warning';
        } else {
            $grade = 'CCC';
            $gradeLabel = 'Perhatian Khusus / Kurang Sehat';
            $gradeBadge = 'danger';
        }

        // Actionable Recommendations
        $recommendations = [];
        if ($currentRatio < 1.2) {
            $recommendations[] = [
                'pilar' => 'Likuiditas',
                'tipe' => 'danger',
                'pesan' => 'Current ratio rendah (' . number_format($currentRatio, 2) . 'x). Tingkatkan penagihan piutang dan batasi kewajiban jangka pendek baru.'
            ];
        }
        if ($npm < 5.0) {
            $recommendations[] = [
                'pilar' => 'Profitabilitas',
                'tipe' => 'warning',
                'pesan' => 'Margin laba bersih (NPM) tipis (' . number_format($npm, 2) . '%). Lakukan efisiensi beban operasional dan evaluasi strategi penetapan harga jual.'
            ];
        }
        if ($dar > 60.0) {
            $recommendations[] = [
                'pilar' => 'Solvabilitas',
                'tipe' => 'danger',
                'pesan' => 'Rasio utang terhadap aset (DAR) tinggi (' . number_format($dar, 2) . '%). Hindari penambahan utang baru dan prioritaskan pelunasan pinjaman berbunga.'
            ];
        }
        if ($tato < 0.6) {
            $recommendations[] = [
                'pilar' => 'Efisiensi',
                'tipe' => 'info',
                'pesan' => 'Perputaran aset lambat (' . number_format($tato, 2) . 'x). Optimalkan utilisasi aset tetap dan dorong percepatan volume transaksi.'
            ];
        }
        if (empty($recommendations)) {
            $recommendations[] = [
                'pilar' => 'Strategis',
                'tipe' => 'success',
                'pesan' => 'Kinerja seluruh pilar keuangan dalam batas ideal. Pertahankan disiplin anggaran dan lanjutkan ekspansi bisnis terukur.'
            ];
        }

        return [
            'z_score' => [
                'value' => $zScore,
                'zone' => $zZone,
                'label' => $zLabel,
                'color' => $zColor,
                'desc' => $zDesc,
                'variables' => [
                    'x1' => ['label' => 'Modal Kerja / Total Aset', 'val' => $x1, 'bobot' => 0.717],
                    'x2' => ['label' => 'Laba Bersih / Total Aset', 'val' => $x2, 'bobot' => 0.847],
                    'x3' => ['label' => 'EBIT / Total Aset', 'val' => $x3, 'bobot' => 3.107],
                    'x4' => ['label' => 'Ekuitas / Total Kewajiban', 'val' => $x4, 'bobot' => 0.420],
                    'x5' => ['label' => 'Penjualan / Total Aset', 'val' => $x5, 'bobot' => 0.998],
                ]
            ],
            'total_score' => $totalScore,
            'grade' => $grade,
            'grade_label' => $gradeLabel,
            'grade_badge' => $gradeBadge,
            'pillars' => [
                'likuiditas' => ['score' => round($scoreLikuiditas), 'max' => 25, 'label' => 'Ketahanan Likuiditas'],
                'profitabilitas' => ['score' => round($scoreProfitabilitas), 'max' => 35, 'label' => 'Daya Hasilkan Laba'],
                'solvabilitas' => ['score' => round($scoreSolvabilitas), 'max' => 25, 'label' => 'Struktur Modal & Solvabilitas'],
                'efisiensi' => ['score' => round($scoreEfisiensi), 'max' => 15, 'label' => 'Efisiensi Operasional & Aset'],
            ],
            'recommendations' => $recommendations
        ];
    }
}
