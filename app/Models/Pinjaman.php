<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pinjaman extends Model
{
    protected $table = 'pinjaman';
    protected $primaryKey = 'id_pinjaman';

    protected $fillable = [
        'no_pinjaman',
        'id_anggota',
        'id_jenis_pinjaman',
        'id_jurnal_pencairan',
        'tanggal_pengajuan',
        'tanggal_persetujuan',
        'tanggal_pencairan',
        'tanggal_jatuh_tempo',
        'jumlah_pinjaman',
        'bunga_pertahun',
        'metode_bunga',
        'tenor',
        'provisi',
        'biaya_admin',
        'total_bunga',
        'total_angsuran',
        'sisa_pokok',
        'sisa_bunga',
        'status',
        'kolektibilitas',
        'akun_kas_bank',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'tanggal_persetujuan' => 'date',
        'tanggal_pencairan' => 'date',
        'tanggal_jatuh_tempo' => 'date',
        'jumlah_pinjaman' => 'decimal:2',
        'bunga_pertahun' => 'decimal:2',
        'provisi' => 'decimal:2',
        'biaya_admin' => 'decimal:2',
        'total_bunga' => 'decimal:2',
        'total_angsuran' => 'decimal:2',
        'sisa_pokok' => 'decimal:2',
        'sisa_bunga' => 'decimal:2',
    ];

    /**
     * Generate nomor pinjaman otomatis
     */
    public static function generateNoPinjaman(): string
    {
        $tahun = date('Y');
        $prefix = "PIN-{$tahun}-";
        
        $last = self::where('no_pinjaman', 'like', $prefix . '%')
            ->orderBy('no_pinjaman', 'desc')
            ->first();

        $newNumber = $last ? (int)substr($last->no_pinjaman, -4) + 1 : 1;
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke Anggota
     */
    public function anggota(): BelongsTo
    {
        return $this->belongsTo(Anggota::class, 'id_anggota', 'id_anggota');
    }

    /**
     * Relasi ke JenisPinjaman
     */
    public function jenisPinjaman(): BelongsTo
    {
        return $this->belongsTo(JenisPinjaman::class, 'id_jenis_pinjaman', 'id_jenis_pinjaman');
    }

    /**
     * Relasi ke Jurnal Pencairan
     */
    public function jurnalPencairan(): BelongsTo
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal_pencairan', 'id_jurnal');
    }

    /**
     * Relasi ke Jadwal Angsuran
     */
    public function jadwal(): HasMany
    {
        return $this->hasMany(PinjamanJadwal::class, 'id_pinjaman', 'id_pinjaman');
    }

    /**
     * Relasi ke Angsuran (realisasi)
     */
    public function angsuran(): HasMany
    {
        return $this->hasMany(PinjamanAngsuran::class, 'id_pinjaman', 'id_pinjaman');
    }

    /**
     * Relasi ke Agunan
     */
    public function agunan(): HasMany
    {
        return $this->hasMany(PinjamanAgunan::class, 'id_pinjaman', 'id_pinjaman');
    }

    /**
     * Relasi ke Approval History
     */
    public function approvalHistory(): HasMany
    {
        return $this->hasMany(ApprovalHistory::class, 'reference_id', 'id_pinjaman')
            ->where('module', 'pinjaman');
    }

    /**
     * Relasi ke User (created_by)
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by', 'id_user');
    }

    /**
     * Get net disbursement (pokok - provisi - admin)
     */
    public function getNetDisbursementAttribute(): float
    {
        return $this->jumlah_pinjaman - $this->provisi - $this->biaya_admin;
    }

    /**
     * Get total paid
     */
    public function getTotalPaidAttribute(): float
    {
        return $this->angsuran()->sum('total_bayar');
    }

    /**
     * Get remaining amount
     */
    public function getRemainingAmountAttribute(): float
    {
        return $this->sisa_pokok + $this->sisa_bunga;
    }

    /**
     * Scope by status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope active loans
     */
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['active', 'disbursed']);
    }
}
