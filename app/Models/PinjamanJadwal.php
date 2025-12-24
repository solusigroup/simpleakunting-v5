<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PinjamanJadwal extends Model
{
    protected $table = 'pinjaman_jadwal';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'id_pinjaman',
        'angsuran_ke',
        'tanggal_jatuh_tempo',
        'pokok',
        'bunga',
        'total_angsuran',
        'sisa_pokok_setelah',
        'status',
    ];

    protected $casts = [
        'tanggal_jatuh_tempo' => 'date',
        'pokok' => 'decimal:2',
        'bunga' => 'decimal:2',
        'total_angsuran' => 'decimal:2',
        'sisa_pokok_setelah' => 'decimal:2',
    ];

    /**
     * Relasi ke Pinjaman
     */
    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class, 'id_pinjaman', 'id_pinjaman');
    }

    /**
     * Check if overdue
     */
    public function getIsOverdueAttribute(): bool
    {
        return $this->status !== 'lunas' && $this->tanggal_jatuh_tempo < now();
    }

    /**
     * Get days overdue
     */
    public function getDaysOverdueAttribute(): int
    {
        if (!$this->is_overdue) {
            return 0;
        }
        return now()->diffInDays($this->tanggal_jatuh_tempo);
    }
}
