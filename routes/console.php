<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use Illuminate\Support\Facades\Schedule;

// Jadwalkan pengecekan kesehatan tenant setiap hari pada jam 08:00 pagi
Schedule::command('tenant:check-health')->dailyAt('08:00');
