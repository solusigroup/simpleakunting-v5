<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = \App\Models\Tenant::first();
if ($tenant) {
    tenancy()->initialize($tenant);
}

use App\Models\Simpanan;
use App\Models\Jurnal;
use App\Models\JurnalDetail;
use App\Models\JenisSimpanan;
use App\Models\Anggota;
use Illuminate\Support\Facades\DB;

$simpanans = Simpanan::whereNull('id_jurnal')->get();
echo "Found " . $simpanans->count() . " simpanan without jurnal.\n";

foreach ($simpanans as $simpanan) {
    DB::beginTransaction();
    try {
        $jenisSimpanan = JenisSimpanan::find($simpanan->id_jenis_simpanan);
        $anggota = Anggota::find($simpanan->id_anggota);
        
        $jurnal = Jurnal::create([
            'no_transaksi' => $simpanan->no_transaksi,
            'tanggal' => $simpanan->tanggal,
            'id_cabang' => $simpanan->id_cabang ?? 1,
            'id_unit_usaha' => $simpanan->id_unit_usaha ?? 1,
            'deskripsi' => ($simpanan->jenis_transaksi === 'setor' ? 'Setoran ' : 'Penarikan ') . 
                           ($jenisSimpanan ? $jenisSimpanan->nama_simpanan : '') . ' - ' . ($anggota ? $anggota->nama_lengkap : ''),
            'sumber_jurnal' => 'Simpanan',
            'is_locked' => false,
        ]);

        if ($simpanan->jenis_transaksi === 'setor') {
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $simpanan->akun_kas_bank,
                'debit' => $simpanan->jumlah,
                'kredit' => 0,
            ]);
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $jenisSimpanan ? $jenisSimpanan->akun_simpanan : '2-2000',
                'debit' => 0,
                'kredit' => $simpanan->jumlah,
            ]);
        } else {
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $jenisSimpanan ? $jenisSimpanan->akun_simpanan : '2-2000',
                'debit' => $simpanan->jumlah,
                'kredit' => 0,
            ]);
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $simpanan->akun_kas_bank,
                'debit' => 0,
                'kredit' => $simpanan->jumlah,
            ]);
        }

        $simpanan->id_jurnal = $jurnal->id_jurnal;
        $simpanan->save();
        DB::commit();
        echo "Created jurnal for {$simpanan->no_transaksi}\n";
    } catch (\Exception $e) {
        DB::rollBack();
        echo "Failed for {$simpanan->no_transaksi}: " . $e->getMessage() . "\n";
    }
}
