<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPinjaman extends Model
{
    protected $table = 'jenis_pinjaman';
    protected $primaryKey = 'id_jenis_pinjaman';

    protected $fillable = [
        'kode_pinjaman',
        'nama_pinjaman',
        'kategori',
        'bunga_pertahun',
        'metode_bunga',
        'tenor_max',
        'plafon_max',
        'provisi_persen',
        'admin_fee',
        'akun_piutang_pinjaman',
        'akun_pendapatan_bunga',
        'akun_pendapatan_provisi',
        'akun_pendapatan_admin',
        'is_active',
    ];

    protected $casts = [
        'bunga_pertahun' => 'decimal:2',
        'plafon_max' => 'decimal:2',
        'provisi_persen' => 'decimal:2',
        'admin_fee' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Konstanta metode bunga
     */
    const METODE_FLAT = 'flat';
    const METODE_ANUITAS = 'anuitas';
    const METODE_EFEKTIF = 'efektif';

    /**
     * Relasi ke Pinjaman
     */
    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'id_jenis_pinjaman', 'id_jenis_pinjaman');
    }

    /**
     * Relasi ke Akun Piutang
     */
    public function akunPiutang()
    {
        return $this->belongsTo(Akun::class, 'akun_piutang_pinjaman', 'kode_akun');
    }

    /**
     * Relasi ke Akun Pendapatan Bunga
     */
    public function akunPendapatanBunga()
    {
        return $this->belongsTo(Akun::class, 'akun_pendapatan_bunga', 'kode_akun');
    }

    /**
     * Scope aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan kategori
     */
    public function scopeKategori($query, $kategori)
    {
        return $query->where('kategori', $kategori);
    }

    /**
     * Get metode bunga options
     */
    public static function getMetodeBungaOptions(): array
    {
        return [
            self::METODE_FLAT => 'Flat (Bunga Tetap)',
            self::METODE_ANUITAS => 'Anuitas (Angsuran Tetap)',
            self::METODE_EFEKTIF => 'Efektif / Sliding Rate',
        ];
    }

    /**
     * Get kategori options
     */
    public static function getKategoriOptions(): array
    {
        return [
            'produktif' => 'Produktif (Modal Usaha)',
            'konsumtif' => 'Konsumtif',
            'darurat' => 'Darurat',
        ];
    }
}
