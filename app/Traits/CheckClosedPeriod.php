<?php

namespace App\Traits;

use App\Models\PeriodeTutupBuku;
use Carbon\Carbon;

/**
 * Checks if a transaction date falls within a closed accounting period.
 * Add `use CheckClosedPeriod;` to any controller that creates transactions.
 * Then call `$this->validatePeriodOpen($tanggal)` before saving.
 */
trait CheckClosedPeriod
{
    /**
     * Check if the given date's period is still open for transactions.
     * Throws exception if period is closed.
     *
     * @param string $tanggal Date string (Y-m-d)
     * @throws \Exception
     */
    protected function validatePeriodOpen(string $tanggal): void
    {
        $date = Carbon::parse($tanggal);
        $bulan = $date->month;
        $tahun = $date->year;

        $closed = PeriodeTutupBuku::where('bulan', $bulan)
            ->where('tahun', $tahun)
            ->where('status', 'tutup')
            ->exists();

        if ($closed) {
            $namaBulan = $date->translatedFormat('F Y');
            throw new \Exception("Periode {$namaBulan} sudah ditutup. Tidak dapat menambah/mengubah transaksi pada periode ini.");
        }

        // Check against first usage date (prevent backdate)
        $perusahaan = \Illuminate\Support\Facades\DB::table('perusahaan')->first();
        if ($perusahaan && $perusahaan->tanggal_mulai_pemakaian) {
            $mulaiPakai = Carbon::parse($perusahaan->tanggal_mulai_pemakaian);
            if ($date->lt($mulaiPakai)) {
                throw new \Exception("Tanggal transaksi tidak boleh sebelum tanggal pertama pemakaian aplikasi ({$mulaiPakai->format('d M Y')}).");
            }
        }
    }
}
