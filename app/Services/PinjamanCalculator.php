<?php

namespace App\Services;

class PinjamanCalculator
{
    /**
     * Calculate loan based on method
     */
    public function calculate(
        float $pokok,
        float $bungaPersen,
        int $tenor,
        string $metode,
        ?string $tanggalPencairan = null
    ): array {
        return match($metode) {
            'flat' => $this->calculateFlat($pokok, $bungaPersen, $tenor, $tanggalPencairan),
            'anuitas' => $this->calculateAnuitas($pokok, $bungaPersen, $tenor, $tanggalPencairan),
            'efektif' => $this->calculateEfektif($pokok, $bungaPersen, $tenor, $tanggalPencairan),
            default => throw new \InvalidArgumentException("Metode bunga tidak valid: {$metode}"),
        };
    }

    /**
     * Metode FLAT
     * Bunga dihitung dari pokok awal, sama setiap bulan
     * Angsuran Pokok = Pokok / Tenor
     * Bunga per bulan = (Pokok × Bunga% / 12)
     */
    public function calculateFlat(float $pokok, float $bungaPersen, int $tenor, ?string $tanggalPencairan = null): array
    {
        $bungaPerBulan = ($pokok * ($bungaPersen / 100)) / 12;
        $pokokPerBulan = $pokok / $tenor;
        $angsuranPerBulan = $pokokPerBulan + $bungaPerBulan;
        $totalBunga = $bungaPerBulan * $tenor;
        $totalAngsuran = $pokok + $totalBunga;

        $jadwal = [];
        $sisaPokok = $pokok;
        $startDate = $tanggalPencairan ? new \DateTime($tanggalPencairan) : new \DateTime();

        for ($i = 1; $i <= $tenor; $i++) {
            $tanggalJatuhTempo = clone $startDate;
            $tanggalJatuhTempo->modify("+{$i} months");

            $sisaPokok -= $pokokPerBulan;

            $jadwal[] = [
                'angsuran_ke' => $i,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                'pokok' => round($pokokPerBulan, 2),
                'bunga' => round($bungaPerBulan, 2),
                'total_angsuran' => round($angsuranPerBulan, 2),
                'sisa_pokok_setelah' => round(max($sisaPokok, 0), 2),
            ];
        }

        return [
            'metode' => 'flat',
            'pokok' => $pokok,
            'bunga_pertahun' => $bungaPersen,
            'tenor' => $tenor,
            'angsuran_per_bulan' => round($angsuranPerBulan, 2),
            'total_bunga' => round($totalBunga, 2),
            'total_angsuran' => round($totalAngsuran, 2),
            'jadwal' => $jadwal,
        ];
    }

    /**
     * Metode ANUITAS
     * Angsuran tetap setiap bulan (pokok + bunga)
     * Bunga semakin kecil, pokok semakin besar
     * PMT = P × (r(1+r)^n) / ((1+r)^n - 1)
     */
    public function calculateAnuitas(float $pokok, float $bungaPersen, int $tenor, ?string $tanggalPencairan = null): array
    {
        $r = ($bungaPersen / 100) / 12; // Monthly interest rate
        
        if ($r == 0) {
            // If interest rate is 0, use simple division
            $angsuranPerBulan = $pokok / $tenor;
        } else {
            // PMT formula
            $angsuranPerBulan = $pokok * ($r * pow(1 + $r, $tenor)) / (pow(1 + $r, $tenor) - 1);
        }

        $jadwal = [];
        $sisaPokok = $pokok;
        $totalBunga = 0;
        $startDate = $tanggalPencairan ? new \DateTime($tanggalPencairan) : new \DateTime();

        for ($i = 1; $i <= $tenor; $i++) {
            $tanggalJatuhTempo = clone $startDate;
            $tanggalJatuhTempo->modify("+{$i} months");

            $bungaBulanIni = $sisaPokok * $r;
            $pokokBulanIni = $angsuranPerBulan - $bungaBulanIni;
            
            // Last installment adjustment
            if ($i == $tenor) {
                $pokokBulanIni = $sisaPokok;
                $angsuranAdjusted = $pokokBulanIni + $bungaBulanIni;
            } else {
                $angsuranAdjusted = $angsuranPerBulan;
            }

            $totalBunga += $bungaBulanIni;
            $sisaPokok -= $pokokBulanIni;

            $jadwal[] = [
                'angsuran_ke' => $i,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                'pokok' => round($pokokBulanIni, 2),
                'bunga' => round($bungaBulanIni, 2),
                'total_angsuran' => round($angsuranAdjusted, 2),
                'sisa_pokok_setelah' => round(max($sisaPokok, 0), 2),
            ];
        }

        return [
            'metode' => 'anuitas',
            'pokok' => $pokok,
            'bunga_pertahun' => $bungaPersen,
            'tenor' => $tenor,
            'angsuran_per_bulan' => round($angsuranPerBulan, 2),
            'total_bunga' => round($totalBunga, 2),
            'total_angsuran' => round($pokok + $totalBunga, 2),
            'jadwal' => $jadwal,
        ];
    }

    /**
     * Metode EFEKTIF (Sliding Rate)
     * Bunga dihitung dari sisa pokok
     * Angsuran pokok tetap, bunga menurun
     * Bunga = Sisa Pokok × (Bunga% / 12)
     */
    public function calculateEfektif(float $pokok, float $bungaPersen, int $tenor, ?string $tanggalPencairan = null): array
    {
        $r = ($bungaPersen / 100) / 12;
        $pokokPerBulan = $pokok / $tenor;

        $jadwal = [];
        $sisaPokok = $pokok;
        $totalBunga = 0;
        $startDate = $tanggalPencairan ? new \DateTime($tanggalPencairan) : new \DateTime();

        for ($i = 1; $i <= $tenor; $i++) {
            $tanggalJatuhTempo = clone $startDate;
            $tanggalJatuhTempo->modify("+{$i} months");

            $bungaBulanIni = $sisaPokok * $r;
            $angsuranBulanIni = $pokokPerBulan + $bungaBulanIni;

            $totalBunga += $bungaBulanIni;
            $sisaPokok -= $pokokPerBulan;

            $jadwal[] = [
                'angsuran_ke' => $i,
                'tanggal_jatuh_tempo' => $tanggalJatuhTempo->format('Y-m-d'),
                'pokok' => round($pokokPerBulan, 2),
                'bunga' => round($bungaBulanIni, 2),
                'total_angsuran' => round($angsuranBulanIni, 2),
                'sisa_pokok_setelah' => round(max($sisaPokok, 0), 2),
            ];
        }

        // Average installment for display
        $avgAngsuran = ($pokok + $totalBunga) / $tenor;

        return [
            'metode' => 'efektif',
            'pokok' => $pokok,
            'bunga_pertahun' => $bungaPersen,
            'tenor' => $tenor,
            'angsuran_per_bulan' => round($avgAngsuran, 2),
            'angsuran_pertama' => $jadwal[0]['total_angsuran'] ?? 0,
            'angsuran_terakhir' => $jadwal[count($jadwal) - 1]['total_angsuran'] ?? 0,
            'total_bunga' => round($totalBunga, 2),
            'total_angsuran' => round($pokok + $totalBunga, 2),
            'jadwal' => $jadwal,
        ];
    }

    /**
     * Calculate early settlement amount
     */
    public function calculateEarlySettlement(float $sisaPokok, float $sisaBunga, int $sisaTenor, string $metode): array
    {
        // For early settlement, typically only remaining principal + some penalty or reduced interest
        $diskonBunga = 0;
        
        if ($metode === 'flat') {
            // Flat: Give 50% discount on remaining interest
            $diskonBunga = $sisaBunga * 0.5;
        } elseif ($metode === 'anuitas' || $metode === 'efektif') {
            // Anuitas/Efektif: No remaining interest needed (already calculated on declining balance)
            $diskonBunga = $sisaBunga;
        }

        return [
            'sisa_pokok' => $sisaPokok,
            'sisa_bunga_sebelum_diskon' => $sisaBunga,
            'diskon_bunga' => round($diskonBunga, 2),
            'bunga_yang_harus_dibayar' => round($sisaBunga - $diskonBunga, 2),
            'total_pelunasan' => round($sisaPokok + ($sisaBunga - $diskonBunga), 2),
        ];
    }
}
