<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tenant = \App\Models\Tenant::first();
tenancy()->initialize($tenant);

$jenis = Illuminate\Support\Facades\DB::table('jenis_simpanan')->get();
echo json_encode($jenis, JSON_PRETTY_PRINT);
