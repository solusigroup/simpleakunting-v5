<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UnitUsaha extends Model
{
    use HasFactory;

    protected $table = 'unit_usaha';

    protected $fillable = [
        'id_cabang',
        'kode_unit',
        'nama_unit',
        'jenis_usaha',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    /**
     * Scope untuk filter berdasarkan cabang.
     */
    public function scopeForCabang($query, $cabangId)
    {
        return $query->where('id_cabang', $cabangId);
    }

    /**
     * Scope untuk unit yang aktif saja.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
