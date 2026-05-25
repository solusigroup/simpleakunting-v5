<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenants = \App\Models\Tenant::all();
foreach($tenants as $tenant) {
    tenancy()->initialize($tenant);
    echo "\n=== Tenant: {$tenant->id} ===\n";
    $simpanan = \Illuminate\Support\Facades\DB::connection('tenant')->table('simpanan')->get();
    echo "Total Simpanan: " . count($simpanan) . "\n";
    foreach($simpanan as $s) {
        echo "Simpanan: " . $s->no_transaksi . " - Jurnal: " . ($s->id_jurnal ?? 'NULL') . "\n";
    }

    $pinjaman = \Illuminate\Support\Facades\DB::connection('tenant')->table('pinjaman')->get();
    echo "Total Pinjaman: " . count($pinjaman) . "\n";
    foreach($pinjaman as $p) {
        echo "Pinjaman: " . $p->no_pinjaman . " - Status: " . $p->status . " - Jurnal: " . ($p->id_jurnal_pencairan ?? 'NULL') . "\n";
    }
}
