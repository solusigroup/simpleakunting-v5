<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CheckTenantHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:check-health';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mengecek tenant yang sudah offline lebih dari 3 hari untuk memantau kepatuhan penggunaan aplikasi.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan status kepatuhan tenant...');

        $threshold = Carbon::now()->subDays(3);

        // Ambil semua tenant beserta data heartbeat-nya
        $tenants = Tenant::leftJoin('tenant_heartbeats', 'tenants.id', '=', 'tenant_heartbeats.tenant_id')
            ->select(
                'tenants.id', 
                'tenants.nama_perusahaan', 
                'tenants.created_at', 
                'tenant_heartbeats.last_seen_at', 
                'tenant_heartbeats.domain'
            )
            ->get();

        $offlineCount = 0;

        foreach ($tenants as $tenant) {
            $lastSeen = $tenant->last_seen_at ? Carbon::parse($tenant->last_seen_at) : null;
            $createdAt = $tenant->created_at ? Carbon::parse($tenant->created_at) : Carbon::now();
            $isOffline = false;
            $lastSeenText = '';

            if (is_null($lastSeen)) {
                // Jika belum pernah mengirim heartbeat, pastikan akun sudah berumur lebih dari 3 hari sebelum dicap offline
                if ($createdAt->lessThan($threshold)) {
                    $isOffline = true;
                    $lastSeenText = 'Belum pernah mengirim sinyal sejak dibuat pada ' . $createdAt->format('d M Y');
                }
            } elseif ($lastSeen->lessThan($threshold)) {
                // Jika terakhir terlihat sudah melebihi 3 hari
                $isOffline = true;
                $lastSeenText = $lastSeen->format('d M Y, H:i');
            }

            if ($isOffline) {
                $offlineCount++;
                $domainText = $tenant->domain ? $tenant->domain : "domain belum terekam";
                
                $message = "🚨 KEPATUHAN HOSTING: Tenant [{$tenant->id} - {$tenant->nama_perusahaan}] ($domainText) terdeteksi OFFLINE lebih dari 3 hari. Status Last Seen: {$lastSeenText}. Segera hubungi penerima hibah.";
                
                // Menampilkan log di terminal/console
                $this->warn($message);
                
                // Mencatat peringatan di file laravel.log (bisa diintegrasikan dengan Slack/Telegram/Discord via channel log)
                Log::warning($message);
            }
        }

        if ($offlineCount === 0) {
            $this->info('Semua tenant terpantau online dan patuh.');
        } else {
            $this->error("Pengecekan selesai. Terdapat {$offlineCount} tenant yang offline dan butuh tindakan segera!");
        }

        return Command::SUCCESS;
    }
}
