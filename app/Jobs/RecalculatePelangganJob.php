<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecalculatePelangganJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. BACKFILL: Hubungkan kembali jurnal lama (Multi-strategy)
            \Illuminate\Support\Facades\DB::statement("
                UPDATE jurnal_umum j
                JOIN penjualan p ON j.id_jurnal = p.id_jurnal
                SET j.id_pelanggan = p.id_pelanggan
                WHERE j.id_pelanggan IS NULL
            ");

            \Illuminate\Support\Facades\DB::statement("
                UPDATE jurnal_umum j
                JOIN penjualan p ON j.no_transaksi = p.no_faktur
                SET j.id_pelanggan = p.id_pelanggan
                WHERE j.id_pelanggan IS NULL
            ");

            $pelanggan = \App\Models\Pelanggan::all();
            foreach ($pelanggan as $p) {
                $netChange = \App\Models\JurnalDetail::whereHas('jurnal', function($q) use ($p) {
                        $q->where('id_pelanggan', $p->id_pelanggan);
                    })
                    ->whereHas('akun', function($q) {
                        $q->where('tipe_akun', 'Piutang');
                    })
                    ->select(\Illuminate\Support\Facades\DB::raw('SUM(debit) - SUM(kredit) as net_change'))
                    ->value('net_change') ?? 0;

                $p->saldo_terkini_piutang = $p->saldo_awal_piutang + $netChange;
                $p->save();
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('RecalculatePelangganJob failed: ' . $e->getMessage());
        }
    }
}
