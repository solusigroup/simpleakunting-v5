<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantHeartbeat extends Model
{
    protected $fillable = [
        'tenant_id',
        'domain',
        'app_version',
        'php_version',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];
}
