<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Simpanan extends Model
{
    protected $table = 'simpanan';
    protected $primaryKey = 'id_simpanan';

    protected $fillable = [
        'no_transaksi',
        'id_anggota',
        'id_jenis_simpanan',
        'id_jurnal',
        'tanggal',
        'jenis_transaksi',
        'jumlah',
        'akun_kas_bank',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jumlah' => 'decimal:2',
    ];

    /**
     * Relasi ke Anggota
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    /**
     * Relasi ke JenisSimpanan
     */
    public function jenisSimpanan(): BelongsTo
    {
        return $this->belongsTo(JenisSimpanan::class, 'id_jenis_simpanan', 'id_jenis_simpanan');
    }

    /**
     * Relasi ke Jurnal
     */
    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal', 'id_jurnal');
    }

    /**
     * Relasi ke User (created_by)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }
}
