<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PinjamanAngsuran extends Model
{
    protected $table = 'pinjaman_angsuran';
    protected $primaryKey = 'id_angsuran';

    protected $fillable = [
        'no_transaksi',
        'id_pinjaman',
        'id_jadwal',
        'id_jurnal',
        'tanggal_bayar',
        'pokok_dibayar',
        'bunga_dibayar',
        'denda',
        'total_bayar',
        'akun_kas_bank',
        'jenis',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'pokok_dibayar' => 'decimal:2',
        'bunga_dibayar' => 'decimal:2',
        'denda' => 'decimal:2',
        'total_bayar' => 'decimal:2',
    ];

    /**
     * Relasi ke Pinjaman
     */
    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class, 'id_pinjaman', 'id_pinjaman');
    }

    /**
     * Relasi ke Jadwal
     */
    public function jadwal(): BelongsTo
    {
        return $this->belongsTo(PinjamanJadwal::class, 'id_jadwal', 'id_jadwal');
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
