<?php

namespace App\Http\Controllers;

use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Persediaan;
use App\Models\Penjualan;
use App\Models\Pembelian;
use App\Models\Simpanan;
use App\Models\Pinjaman;
use App\Models\JenisSimpanan;
use App\Models\JenisPinjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Saldo Kas & Bank - dari jurnal_detail
        $totalKasBank = DB::table('jurnal_detail')
            ->join('akun', 'jurnal_detail.kode_akun', '=', 'akun.kode_akun')
            ->where('akun.tipe_akun', 'Kas & Bank')
            ->sum(DB::raw('jurnal_detail.debit - jurnal_detail.kredit'));

        // 2. Summary Cards - Piutang & Utang
        $totalPiutang = DB::table('jurnal_detail')
            ->join('akun', 'jurnal_detail.kode_akun', '=', 'akun.kode_akun')
            ->where('akun.tipe_akun', 'Piutang')
            ->sum(DB::raw('jurnal_detail.debit - jurnal_detail.kredit'));

        $totalUtang = DB::table('jurnal_detail')
            ->join('akun', 'jurnal_detail.kode_akun', '=', 'akun.kode_akun')
            ->whereIn('akun.tipe_akun', ['Utang Usaha', 'Kewajiban Lancar Lainnya', 'Kewajiban Jangka Panjang'])
            ->sum(DB::raw('jurnal_detail.kredit - jurnal_detail.debit'));

        // 3. Nilai Persediaan - dari jurnal_detail
        $nilaiPersediaan = DB::table('jurnal_detail')
            ->join('akun', 'jurnal_detail.kode_akun', '=', 'akun.kode_akun')
            ->where('akun.tipe_akun', 'Persediaan')
            ->sum(DB::raw('jurnal_detail.debit - jurnal_detail.kredit'));

        // 4. Simpanan Summary by Type
        $simpananByType = collect([]);
        $totalSimpanan = 0;
        
        try {
            $simpananByType = DB::table('simpanan')
                ->join('jenis_simpanan', 'simpanan.id_jenis_simpanan', '=', 'jenis_simpanan.id_jenis_simpanan')
                ->select(
                    'jenis_simpanan.nama_simpanan',
                    'jenis_simpanan.tipe',
                    DB::raw("SUM(CASE WHEN simpanan.jenis_transaksi = 'setor' THEN simpanan.jumlah ELSE -simpanan.jumlah END) as saldo")
                )
                ->groupBy('jenis_simpanan.id_jenis_simpanan', 'jenis_simpanan.nama_simpanan', 'jenis_simpanan.tipe')
                ->get();

            $totalSimpanan = $simpananByType->sum('saldo');
        } catch (\Exception $e) {
            // Tables don't exist yet
        }

        // 3. Pinjaman Summary by Type (Active loans only)
        $pinjamanByType = collect([]);
        $totalPinjamanAktif = 0;
        
        try {
            $pinjamanByType = DB::table('pinjaman')
                ->join('jenis_pinjaman', 'pinjaman.id_jenis_pinjaman', '=', 'jenis_pinjaman.id_jenis_pinjaman')
                ->select(
                    'jenis_pinjaman.nama_pinjaman',
                    'jenis_pinjaman.kategori',
                    DB::raw('SUM(pinjaman.sisa_pokok) as sisa_pokok'),
                    DB::raw('COUNT(pinjaman.id_pinjaman) as jumlah_aktif')
                )
                ->whereIn('pinjaman.status', ['active', 'disbursed'])
                ->groupBy('jenis_pinjaman.id_jenis_pinjaman', 'jenis_pinjaman.nama_pinjaman', 'jenis_pinjaman.kategori')
                ->get();

            $totalPinjamanAktif = $pinjamanByType->sum('sisa_pokok');
        } catch (\Exception $e) {
            // Tables don't exist yet
        }

        // 4. Trend Chart Data - Penjualan vs Pembelian (Last 6 Months)
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();

        $penjualan = Penjualan::select(
                DB::raw("DATE_FORMAT(tanggal_faktur, '%Y-%m') as periode"),
                DB::raw('SUM(total) as total')
            )
            ->where('tanggal_faktur', '>=', $sixMonthsAgo)
            ->groupBy('periode')
            ->get()
            ->keyBy('periode');

        $pembelian = Pembelian::select(
                DB::raw("DATE_FORMAT(tanggal_faktur, '%Y-%m') as periode"),
                DB::raw('SUM(total) as total')
            )
            ->where('tanggal_faktur', '>=', $sixMonthsAgo)
            ->groupBy('periode')
            ->get()
            ->keyBy('periode');

        // Merge and format for Chart.js
        $labels = [];
        $salesData = [];
        $purchasesData = [];
        
        $currentDate = $sixMonthsAgo->copy();
        $now = now()->endOfMonth();

        while ($currentDate <= $now) {
            $periode = $currentDate->format('Y-m');
            $label = $currentDate->format('M Y');
            
            $labels[] = $label;
            $salesData[] = $penjualan[$periode]->total ?? 0;
            $purchasesData[] = $pembelian[$periode]->total ?? 0;
            
            $currentDate->addMonth();
        }

        // 5. Pendapatan vs Biaya Chart (From jurnal_detail with akun klasifikasi)
        $pendapatanData = [];
        $biayaData = [];
        
        try {
            $pendapatanBiaya = DB::table('jurnal_detail')
                ->join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
                ->join('akun', 'jurnal_detail.kode_akun', '=', 'akun.kode_akun')
                ->select(
                    DB::raw("DATE_FORMAT(jurnal_umum.tanggal, '%Y-%m') as periode"),
                    'akun.tipe_akun',
                    DB::raw('SUM(jurnal_detail.kredit - jurnal_detail.debit) as saldo')
                )
                ->where('jurnal_umum.tanggal', '>=', $sixMonthsAgo)
                ->whereIn('akun.tipe_akun', ['Pendapatan', 'Pendapatan Lainnya', 'HPP', 'Beban', 'Beban Lainnya'])
                ->groupBy('periode', 'akun.tipe_akun')
                ->get();

            $currentDate = $sixMonthsAgo->copy();
            while ($currentDate <= $now) {
                $periode = $currentDate->format('Y-m');
                
                $pendapatan = $pendapatanBiaya->where('periode', $periode)->whereIn('tipe_akun', ['Pendapatan', 'Pendapatan Lainnya']);
                $biaya = $pendapatanBiaya->where('periode', $periode)->whereIn('tipe_akun', ['HPP', 'Beban', 'Beban Lainnya']);
                
                $pendapatanData[] = abs($pendapatan->sum('saldo'));
                $biayaData[] = abs($biaya->sum('saldo'));
                
                $currentDate->addMonth();
            }
        } catch (\Exception $e) {
            // Tables don't exist yet, fill with zeros
            foreach ($labels as $label) {
                $pendapatanData[] = 0;
                $biayaData[] = 0;
            }
        }

        return view('dashboard.index', [
            'totalKasBank' => $totalKasBank,
            'totalPiutang' => $totalPiutang,
            'totalUtang' => $totalUtang,
            'nilaiPersediaan' => $nilaiPersediaan,
            'simpananByType' => $simpananByType,
            'totalSimpanan' => $totalSimpanan,
            'pinjamanByType' => $pinjamanByType,
            'totalPinjamanAktif' => $totalPinjamanAktif,
            'chartLabels' => json_encode($labels),
            'chartSales' => json_encode($salesData),
            'chartPurchases' => json_encode($purchasesData),
            'chartPendapatan' => json_encode($pendapatanData),
            'chartBiaya' => json_encode($biayaData),
        ]);
    }
}

