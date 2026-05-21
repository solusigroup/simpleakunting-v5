<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Pelanggan;
use App\Models\Pemasok;
use App\Models\Persediaan;
use App\Models\JurnalDetail;
use Illuminate\Support\Facades\DB;

class ReconcileBalances extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:reconcile-balances';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reconcile denormalized balances (Piutang, Hutang, Stok) with transaction details';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting balance reconciliation...');

        DB::beginTransaction();

        try {
            // 1. Reconcile Piutang (Pelanggan)
            $this->info('Reconciling Pelanggan balances...');
            $pelanggan = Pelanggan::all();
            $countPelanggan = 0;
            foreach ($pelanggan as $p) {
                $netChange = JurnalDetail::whereHas('jurnal', function($q) use ($p) {
                        $q->where('id_pelanggan', $p->id_pelanggan);
                    })
                    ->whereHas('akun', function($q) {
                        $q->where('tipe_akun', 'Piutang');
                    })
                    ->select(DB::raw('SUM(debit) - SUM(kredit) as net_change'))
                    ->value('net_change') ?? 0;

                $newBalance = $p->saldo_awal_piutang + $netChange;
                if ($p->saldo_terkini_piutang != $newBalance) {
                    $p->saldo_terkini_piutang = $newBalance;
                    $p->save();
                    $countPelanggan++;
                }
            }
            $this->line("Fixed $countPelanggan Pelanggan records.");

            // 2. Reconcile Hutang (Pemasok)
            $this->info('Reconciling Pemasok balances...');
            $pemasok = Pemasok::all();
            $countPemasok = 0;
            foreach ($pemasok as $v) {
                $netChange = JurnalDetail::whereHas('jurnal', function($q) use ($v) {
                        $q->where('id_pemasok', $v->id_pemasok);
                    })
                    ->whereHas('akun', function($q) {
                        $q->where('tipe_akun', 'Utang Usaha');
                    })
                    ->select(DB::raw('SUM(kredit) - SUM(debit) as net_change'))
                    ->value('net_change') ?? 0;

                $newBalance = $v->saldo_awal_hutang + $netChange;
                if ($v->saldo_terkini_hutang != $newBalance) {
                    $v->saldo_terkini_hutang = $newBalance;
                    $v->save();
                    $countPemasok++;
                }
            }
            $this->line("Fixed $countPemasok Pemasok records.");

            // 3. Reconcile Stok (Persediaan)
            $this->info('Reconciling Stok Persediaan...');
            $persediaan = Persediaan::all();
            $countStok = 0;
            foreach ($persediaan as $item) {
                $masuk = DB::table('kartu_stok')->where('id_barang', $item->id_barang)->where('tipe_transaksi', 'IN')->sum('kuantitas') ?? 0;
                $keluar = DB::table('kartu_stok')->where('id_barang', $item->id_barang)->where('tipe_transaksi', 'OUT')->sum('kuantitas') ?? 0;
                
                // Note: stok_awal is already recorded as IN in kartu_stok upon creation/update.
                // We just calculate SUM(IN) - SUM(OUT)
                $newStok = $masuk - $keluar;
                if ($item->stok_saat_ini != $newStok) {
                    $item->stok_saat_ini = $newStok;
                    $item->save();
                    $countStok++;
                }
            }
            $this->line("Fixed $countStok Persediaan records.");

            DB::commit();
            $this->info('Reconciliation completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('Reconciliation failed: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
