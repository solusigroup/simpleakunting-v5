<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class RecalculatePemasokJob implements ShouldQueue
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
                JOIN pembelian p ON j.id_jurnal = p.id_jurnal
                SET j.id_pemasok = p.id_pemasok
                WHERE j.id_pemasok IS NULL
            ");

            \Illuminate\Support\Facades\DB::statement("
                UPDATE jurnal_umum j
                JOIN pembelian p ON j.no_transaksi = p.no_faktur_pembelian
                SET j.id_pemasok = p.id_pemasok
                WHERE j.id_pemasok IS NULL
            ");

            $pemasok = \App\Models\Pemasok::all();
            foreach ($pemasok as $v) {
                $netChange = \App\Models\JurnalDetail::whereHas('jurnal', function($q) use ($v) {
                        $q->where('id_pemasok', $v->id_pemasok);
                    })
                    ->whereHas('akun', function($q) {
                        $q->where('tipe_akun', 'Utang Usaha');
                    })
                    ->select(\Illuminate\Support\Facades\DB::raw('SUM(kredit) - SUM(debit) as net_change'))
                    ->value('net_change') ?? 0;

                $v->saldo_terkini_hutang = $v->saldo_awal_hutang + $netChange;
                $v->save();
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('RecalculatePemasokJob failed: ' . $e->getMessage());
        }
    }
}
