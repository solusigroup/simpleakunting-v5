<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\MonitoringController;

Route::post('/v1/heartbeat', [MonitoringController::class, 'handleHeartbeat']);

Route::get('/v1/debug-log-xyz', function () {
    $logFile = storage_path('logs/laravel.log');
    if (file_exists($logFile)) {
        // Return the last 50 lines
        $lines = file($logFile);
        $lastLines = array_slice($lines, -50);
        return response()->json(['log' => implode("", $lastLines)]);
    }
    return response()->json(['log' => 'No log file found']);
});
