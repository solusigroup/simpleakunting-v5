<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$stuck = \App\Models\PenjualanPenawaran::where('status', 'Dikonversi')
    ->whereNotIn('id_penawaran', \App\Models\Penjualan::whereNotNull('id_penawaran')->pluck('id_penawaran'))
    ->get();
foreach($stuck as $p) {
    $p->update(['status' => 'Draft']);
    echo "Fixed {$p->id_penawaran}\n";
}
echo "Done\n";
