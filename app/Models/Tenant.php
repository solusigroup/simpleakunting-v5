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
     * Custom columns on the tenants table.
     * These are stored as actual columns, not in the JSON `data` column.
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
