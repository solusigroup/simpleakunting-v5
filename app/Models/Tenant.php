<?php

namespace App\Models;

use Stancl\Tenancy\Database\Models\Tenant as BaseTenant;
use Stancl\Tenancy\Contracts\TenantWithDatabase;
use Stancl\Tenancy\Database\Concerns\HasDatabase;
use Stancl\Tenancy\Database\Concerns\HasDomains;

class Tenant extends BaseTenant implements TenantWithDatabase
{
    use HasDatabase, HasDomains;

    /**
     * Override GeneratesIds trait — kita menggunakan custom string ID (e.g. 'demo'),
     * bukan auto-increment. Trait GeneratesIds mendefinisikan getIncrementing()
     * sebagai METHOD, jadi property $incrementing = false tidak berpengaruh.
     */
    public function getIncrementing(): bool
    {
        return false;
    }

    public function getKeyType(): string
    {
        return 'string';
    }

    /**
     * Custom columns on the tenants table.
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'nama_perusahaan',
            'email',
            'is_active',
        ];
    }
}