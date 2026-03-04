<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class CabangScope implements Scope
{
    /**
     * Auto-filter data berdasarkan cabang user.
     *
     * - Staff/Manajer: hanya lihat data cabang sendiri
     * - Admin/Superuser: lihat semua, atau filter by session('active_cabang')
     * - Data lama (id_cabang = NULL) tetap terlihat semua (backward-compatible)
     */
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        if (!$user) return;

        $table = $model->getTable();

        if ($user->hasRole(['superuser', 'admin'])) {
            // Admin/superuser: filter jika ada active cabang di session
            $activeCabang = session('active_cabang');
            $activeUnit = session('active_unit');

            if ($activeUnit) {
                $builder->where(function ($q) use ($table, $activeUnit) {
                    $q->where($table . '.id_unit_usaha', $activeUnit)
                      ->orWhereNull($table . '.id_unit_usaha');
                });
            } elseif ($activeCabang) {
                $builder->where(function ($q) use ($table, $activeCabang) {
                    $q->where($table . '.id_cabang', $activeCabang)
                      ->orWhereNull($table . '.id_cabang');
                });
            }
            // Jika tidak ada session aktif, tampilkan semua (konsolidasi)
        } else {
            // Staff/manajer: hanya lihat data cabang sendiri + data lama (NULL)
            $userCabang = $user->id_cabang;
            if ($userCabang) {
                $builder->where(function ($q) use ($table, $userCabang) {
                    $q->where($table . '.id_cabang', $userCabang)
                      ->orWhereNull($table . '.id_cabang');
                });
            }
        }
    }
}
