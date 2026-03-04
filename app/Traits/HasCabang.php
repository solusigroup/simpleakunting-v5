<?php

namespace App\Traits;

use App\Models\Cabang;
use App\Models\UnitUsaha;
use App\Scopes\CabangScope;

trait HasCabang
{
    public static function bootHasCabang()
    {
        // Auto-apply CabangScope for filtering
        static::addGlobalScope(new CabangScope);

        // Auto-set id_cabang + id_unit_usaha saat create
        static::creating(function ($model) {
            $user = auth()->user();
            if (!$user) return;

            // Auto-set cabang dari user jika belum di-set
            if (empty($model->id_cabang) && $user->id_cabang) {
                $model->id_cabang = $user->id_cabang;
            }

            // Auto-set unit dari session jika belum di-set
            if (empty($model->id_unit_usaha) && session('active_unit')) {
                $model->id_unit_usaha = session('active_unit');
            }
        });
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function unitUsaha()
    {
        return $this->belongsTo(UnitUsaha::class, 'id_unit_usaha');
    }

    /**
     * Scope filter by cabang.
     */
    public function scopeForCabang($query, $cabangId)
    {
        return $query->where($this->getTable() . '.id_cabang', $cabangId);
    }

    /**
     * Scope filter by unit usaha.
     */
    public function scopeForUnit($query, $unitId)
    {
        return $query->where($this->getTable() . '.id_unit_usaha', $unitId);
    }
}
