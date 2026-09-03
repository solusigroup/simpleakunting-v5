<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class GeneratePdfReportJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    protected $downloadId;
    protected $tenantId;

    public function __construct($downloadId)
    {
        $this->downloadId = $downloadId;
        $this->tenantId = tenant('id');
    }

    public function handle(): void
    {
        // Re-initialize tenant context inside the job manually just in case
        if ($this->tenantId) {
            tenancy()->initialize($this->tenantId);
        }

        \App\Models\Jurnal::$applyApprovalFilter = true;

        $report = \App\Models\ReportDownload::find($this->downloadId);
        if (!$report) {
            return;
        }

        $report->update(['status' => 'processing']);

        try {
            $controller = new \App\Http\Controllers\LaporanController();
            $request = new \Illuminate\Http\Request();
            $params = json_decode($report->params, true) ?? [];
            $request->merge($params);

            // We need to bypass the download response and capture the PDF content instead
            // We can do this by slightly modifying LaporanController, OR we can reproduce the query here.
            // A much cleaner way is to create a GenerateData method in LaporanController, but to avoid large refactoring,
            // we will reproduce the minimal PDF generation block here.

            if ($report->nama_laporan == 'Neraca') {
                $pdfData = $this->generateNeraca($request);
                $view = 'laporan.pdf.neraca';
            } elseif ($report->nama_laporan == 'Laba Rugi') {
                $pdfData = $this->generateLabaRugi($request);
                $view = 'laporan.pdf.labarugi';
            } else {
                throw new \Exception("Laporan tidak didukung");
            }

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView($view, $pdfData);
            $pdf->setPaper('a4', 'portrait');
            
            $fileName = strtolower(str_replace(' ', '_', $report->nama_laporan)) . '_' . time() . '.pdf';
            $filePath = 'reports/' . $this->tenantId . '/' . $fileName;
            
            \Illuminate\Support\Facades\Storage::disk('public')->put($filePath, $pdf->output());

            $report->update([
                'status' => 'completed',
                'file_path' => $filePath
            ]);
        } catch (\Exception $e) {
            $report->update([
                'status' => 'failed',
                'error_message' => $e->getMessage()
            ]);
        }
    }

    private function generateNeraca($request)
    {
        $perTanggal = $request->input('per_tanggal', date('Y-m-d'));
        $perusahaan = \Illuminate\Support\Facades\DB::table('perusahaan')->find(1);
        $cabangId = $request->input('id_cabang');
        $unitId = $request->input('id_unit_usaha');
        $projectId = $request->input('id_project');

        $akunNeraca = \App\Models\Akun::whereIn('tipe_akun', [
            'Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya', 'Aset Tetap',
            'Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang', 'Ekuitas'
        ])->orderBy('kode_akun')->get();

        $aggregated = \App\Models\JurnalDetail::join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_umum.tanggal', '<=', $perTanggal)
            ->when($cabangId, fn($q) => $q->where('jurnal_umum.id_cabang', $cabangId))
            ->when($unitId, fn($q) => $q->where('jurnal_umum.id_unit_usaha', $unitId))
            ->when($projectId, fn($q) => $q->where('jurnal_umum.id_project', $projectId))
            ->groupBy('jurnal_detail.kode_akun')
            ->select(
                'jurnal_detail.kode_akun',
                \Illuminate\Support\Facades\DB::raw('SUM(jurnal_detail.debit) as total_debit'),
                \Illuminate\Support\Facades\DB::raw('SUM(jurnal_detail.kredit) as total_kredit')
            )
            ->get()
            ->keyBy('kode_akun');

        $laporan = $akunNeraca->map(function ($akun) use ($aggregated) {
            $row = $aggregated->get($akun->kode_akun);
            $totalDebit = $row ? $row->total_debit : 0;
            $totalKredit = $row ? $row->total_kredit : 0;

            return [
                'kode' => $akun->kode_akun,
                'nama' => $akun->nama_akun,
                'tipe' => $akun->tipe_akun,
                'saldo' => $akun->saldo_normal == 'Debit' ? $totalDebit - $totalKredit : $totalKredit - $totalDebit,
            ];
        });

        $aktivaLancar = $laporan->whereIn('tipe', ['Kas & Bank', 'Piutang', 'Persediaan', 'Aset Lancar Lainnya'])->values();
        $aktivaTetap = $laporan->where('tipe', 'Aset Tetap')->values();
        $kewajiban = $laporan->whereIn('tipe', ['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang'])->values();
        $modal = $laporan->where('tipe', 'Ekuitas')->values();

        // Direct call to public hitungLabaRugi
        $controller = new \App\Http\Controllers\LaporanController();
        $labaBersih = $controller->hitungLabaRugi($perTanggal, $cabangId, $unitId, $projectId);

        $subtitle = 'Per Tanggal ' . date('d F Y', strtotime($perTanggal));
        if ($projectId) {
            $project = \App\Models\Project::find($projectId);
            if ($project) {
                $subtitle .= ' | Proyek: ' . $project->nama_project;
            }
        }

        return [
            'title' => 'LAPORAN NERACA',
            'subtitle' => $subtitle,
            'perusahaan' => $perusahaan,
            'aktivaLancar' => $aktivaLancar,
            'aktivaTetap' => $aktivaTetap,
            'kewajiban' => $kewajiban,
            'modal' => $modal,
            'totalAktivaLancar' => $aktivaLancar->sum('saldo'),
            'totalAktivaTetap' => $aktivaTetap->sum('saldo'),
            'totalAktiva' => $aktivaLancar->sum('saldo') + $aktivaTetap->sum('saldo'),
            'totalKewajiban' => $kewajiban->sum('saldo'),
            'totalModal' => $modal->sum('saldo'),
            'labaBersih' => $labaBersih,
            'totalPasiva' => $kewajiban->sum('saldo') + $modal->sum('saldo') + $labaBersih,
            'showSignatures' => true,
            'tanggal' => date('d F Y', strtotime($perTanggal)),
        ];
    }

    private function generateLabaRugi($request)
    {
        $startDate = $request->input('start_date', date('Y-m-01'));
        $endDate = $request->input('end_date', date('Y-m-d'));
        $perusahaan = \Illuminate\Support\Facades\DB::table('perusahaan')->find(1);
        $cabangId = $request->input('id_cabang');
        $unitId = $request->input('id_unit_usaha');
        $projectId = $request->input('id_project');

        $akunLabaRugi = \App\Models\Akun::whereIn('tipe_akun', [
            'Pendapatan', 'Pendapatan Lainnya', 'HPP', 'Beban', 'Beban Lainnya'
        ])->orderBy('kode_akun')->get();

        $aggregated = \App\Models\JurnalDetail::join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->whereBetween('jurnal_umum.tanggal', [$startDate, $endDate])
            ->when($cabangId, fn($q) => $q->where('jurnal_umum.id_cabang', $cabangId))
            ->when($unitId, fn($q) => $q->where('jurnal_umum.id_unit_usaha', $unitId))
            ->when($projectId, fn($q) => $q->where('jurnal_umum.id_project', $projectId))
            ->groupBy('jurnal_detail.kode_akun')
            ->select(
                'jurnal_detail.kode_akun',
                \Illuminate\Support\Facades\DB::raw('SUM(jurnal_detail.debit) as total_debit'),
                \Illuminate\Support\Facades\DB::raw('SUM(jurnal_detail.kredit) as total_kredit')
            )
            ->get()
            ->keyBy('kode_akun');

        $laporan = $akunLabaRugi->map(function ($akun) use ($aggregated) {
            $row = $aggregated->get($akun->kode_akun);
            $totalDebit = $row ? $row->total_debit : 0;
            $totalKredit = $row ? $row->total_kredit : 0;

            return [
                'kode' => $akun->kode_akun,
                'nama' => $akun->nama_akun,
                'tipe' => $akun->tipe_akun,
                'saldo' => $akun->saldo_normal == 'Kredit' ? $totalKredit - $totalDebit : $totalDebit - $totalKredit,
            ];
        });

        $pendapatan = $laporan->whereIn('tipe', ['Pendapatan'])->values();
        $hpp = $laporan->where('tipe', 'HPP')->values();
        $beban = $laporan->where('tipe', 'Beban')->values();
        $pendapatanLain = $laporan->where('tipe', 'Pendapatan Lainnya')->values();
        $bebanLain = $laporan->where('tipe', 'Beban Lainnya')->values();

        $labaKotor = $pendapatan->sum('saldo') - $hpp->sum('saldo');
        $labaOperasional = $labaKotor - $beban->sum('saldo');

        $subtitle = 'Periode ' . date('d F Y', strtotime($startDate)) . ' s/d ' . date('d F Y', strtotime($endDate));
        if ($projectId) {
            $project = \App\Models\Project::find($projectId);
            if ($project) {
                $subtitle .= ' | Proyek: ' . $project->nama_project;
            }
        }

        return [
            'title' => 'LAPORAN LABA RUGI',
            'subtitle' => $subtitle,
            'perusahaan' => $perusahaan,
            'pendapatan' => $pendapatan,
            'hpp' => $hpp,
            'beban' => $beban,
            'pendapatanLain' => $pendapatanLain,
            'bebanLain' => $bebanLain,
            'totalPendapatan' => $pendapatan->sum('saldo'),
            'totalHpp' => $hpp->sum('saldo'),
            'labaKotor' => $labaKotor,
            'totalBeban' => $beban->sum('saldo'),
            'labaOperasional' => $labaOperasional,
            'totalPendapatanLain' => $pendapatanLain->sum('saldo'),
            'totalBebanLain' => $bebanLain->sum('saldo'),
            'labaBersih' => $labaOperasional + $pendapatanLain->sum('saldo') - $bebanLain->sum('saldo'),
            'showSignatures' => true,
            'tanggal' => date('d F Y', strtotime($endDate)),
        ];
    }
}
