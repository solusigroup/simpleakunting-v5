<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CalculateFixedAssetDepreciation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'asset:depreciate {--month= : Bulan (1-12)} {--year= : Tahun (YYYY)}';
    protected $description = 'Menghitung depresiasi aset tetap secara otomatis untuk bulan berjalan';

    public function handle()
    {
        $currentMonth = $this->option('month') ?: \Carbon\Carbon::now()->month;
        $currentYear = $this->option('year') ?: \Carbon\Carbon::now()->year;

        $this->info("Memulai proses depresiasi aset untuk periode $currentMonth/$currentYear...");

        $assets = \App\Models\FixedAsset::with('group')
                    ->where('status', 'Aktif')
                    ->whereRaw('nilai_buku_saat_ini > nilai_residu')
                    ->get();

        $count = 0;

        foreach ($assets as $asset) {
            // Check if already depreciated this month
            $alreadyDepreciated = \App\Models\FixedAssetDepreciation::where('aset_id', $asset->id)
                                    ->where('periode_bulan', $currentMonth)
                                    ->where('periode_tahun', $currentYear)
                                    ->exists();

            if ($alreadyDepreciated) continue;

            $group = $asset->group;
            if (!$group) continue;

            // Straight line calculation
            $depreciableAmount = $asset->harga_perolehan - $asset->nilai_residu;
            if ($depreciableAmount <= 0) continue;

            $monthlyDepreciation = round($depreciableAmount / $asset->umur_ekonomis_bulan, 2);

            // Adjust last month depreciation if it exceeds book value
            if (($asset->nilai_buku_saat_ini - $monthlyDepreciation) < $asset->nilai_residu) {
                $monthlyDepreciation = $asset->nilai_buku_saat_ini - $asset->nilai_residu;
            }

            if ($monthlyDepreciation <= 0) continue;

            $jurnalId = null;

            // Create Auto Journal
            if ($group->akun_akumulasi_penyusutan && $group->akun_beban_penyusutan) {
                // Determine branch if available
                $cabangId = $asset->cabang_id ?? null;
                
                // Get Jurnal No
                $lastJurnal = \App\Models\Jurnal::orderBy('id_jurnal', 'desc')->first();
                $nextId = $lastJurnal ? $lastJurnal->id_jurnal + 1 : 1;
                $noTransaksi = 'DP-' . date('Ymd') . '-' . str_pad($nextId, 4, '0', STR_PAD_LEFT);

                $jurnal = \App\Models\Jurnal::create([
                    'no_transaksi' => $noTransaksi,
                    'tanggal' => \Carbon\Carbon::create($currentYear, $currentMonth, 1)->endOfMonth()->toDateString(),
                    'deskripsi' => 'Penyusutan Aset: ' . $asset->nama_aset . ' (Periode ' . $currentMonth . '/' . $currentYear . ')',
                    'sumber_jurnal' => 'Aset Tetap',
                    'is_locked' => 0,
                    'cabang_id' => $cabangId
                ]);

                $jurnalId = $jurnal->id_jurnal;

                // Debit: Beban Penyusutan
                \App\Models\JurnalDetail::create([
                    'id_jurnal' => $jurnalId,
                    'kode_akun' => $group->akun_beban_penyusutan,
                    'debit' => $monthlyDepreciation,
                    'kredit' => 0,
                ]);

                // Kredit: Akumulasi Penyusutan
                \App\Models\JurnalDetail::create([
                    'id_jurnal' => $jurnalId,
                    'kode_akun' => $group->akun_akumulasi_penyusutan,
                    'debit' => 0,
                    'kredit' => $monthlyDepreciation,
                ]);

            }

            // Record History
            \App\Models\FixedAssetDepreciation::create([
                'aset_id' => $asset->id,
                'periode_bulan' => $currentMonth,
                'periode_tahun' => $currentYear,
                'nilai_penyusutan' => $monthlyDepreciation,
                'jurnal_id' => $jurnalId
            ]);

            // Update Book Value
            $asset->nilai_buku_saat_ini -= $monthlyDepreciation;
            $asset->save();

            $count++;
        }

        $this->info("Selesai! $count aset berhasil disusutkan pada periode $currentMonth/$currentYear.");
    }
}
