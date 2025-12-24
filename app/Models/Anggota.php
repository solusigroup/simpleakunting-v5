<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Anggota extends Model
{
    protected $table = 'anggota';
    protected $primaryKey = 'id_anggota';

    protected $fillable = [
        'no_anggota',
        'nik',
        'nama_lengkap',
        'jenis_kelamin',
        'alamat',
        'telepon',
        'email',
        'pekerjaan',
        'foto',
        'tanggal_daftar',
        'status',
        'tanggal_keluar',
    ];

    protected $casts = [
        'tanggal_daftar' => 'date',
        'tanggal_keluar' => 'date',
    ];

    /**
     * Generate nomor anggota otomatis
     */
    public static function generateNoAnggota(): string
    {
        $tahun = date('Y');
        $prefix = "ANG-{$tahun}-";
        
        $lastAnggota = self::where('no_anggota', 'like', $prefix . '%')
            ->orderBy('no_anggota', 'desc')
            ->first();

        if ($lastAnggota) {
            $lastNumber = (int) substr($lastAnggota->no_anggota, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relasi ke Simpanan
     */
    public function simpanan(): HasMany
    {
        return $this->hasMany(Simpanan::class, 'id_anggota', 'id_anggota');
    }

    /**
     * Relasi ke Pinjaman
     */
    public function pinjaman(): HasMany
    {
        return $this->hasMany(Pinjaman::class, 'id_anggota', 'id_anggota');
    }

    /**
     * Hitung total simpanan per jenis
     */
    public function getTotalSimpananAttribute(): array
    {
        return [
            'pokok' => $this->simpanan()
                ->whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'pokok'))
                ->selectRaw('SUM(CASE WHEN jenis_transaksi = "setor" THEN jumlah ELSE -jumlah END) as total')
                ->value('total') ?? 0,
            'wajib' => $this->simpanan()
                ->whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'wajib'))
                ->selectRaw('SUM(CASE WHEN jenis_transaksi = "setor" THEN jumlah ELSE -jumlah END) as total')
                ->value('total') ?? 0,
            'sukarela' => $this->simpanan()
                ->whereHas('jenisSimpanan', fn($q) => $q->where('tipe', 'sukarela'))
                ->selectRaw('SUM(CASE WHEN jenis_transaksi = "setor" THEN jumlah ELSE -jumlah END) as total')
                ->value('total') ?? 0,
        ];
    }

    /**
     * Hitung total pinjaman aktif
     */
    public function getTotalPinjamanAktifAttribute(): float
    {
        return $this->pinjaman()
            ->whereIn('status', ['active', 'disbursed'])
            ->sum('sisa_pokok');
    }

    /**
     * Scope untuk anggota aktif
     */
    public function scopeAktif($query)
    {
        return $query->where('status', 'aktif');
    }
}
