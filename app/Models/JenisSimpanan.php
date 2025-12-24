<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisSimpanan extends Model
{
    protected $table = 'jenis_simpanan';
    protected $primaryKey = 'id_jenis_simpanan';

    protected $fillable = [
        'kode_simpanan',
        'nama_simpanan',
        'tipe',
        'bunga_pertahun',
        'akun_simpanan',
        'akun_bunga',
        'is_active',
    ];

    protected $casts = [
        'bunga_pertahun' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Relasi ke Simpanan
     */
    public function simpanan(): HasMany
    {
        return $this->hasMany(Simpanan::class, 'id_jenis_simpanan', 'id_jenis_simpanan');
    }

    /**
     * Relasi ke Akun Simpanan
     */
    public function akunSimpanan()
    {
        return $this->belongsTo(Akun::class, 'akun_simpanan', 'kode_akun');
    }

    /**
     * Relasi ke Akun Bunga
     */
    public function akunBunga()
    {
        return $this->belongsTo(Akun::class, 'akun_bunga', 'kode_akun');
    }

    /**
     * Scope aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan tipe
     */
    public function scopeTipe($query, $tipe)
    {
        return $query->where('tipe', $tipe);
    }
}
