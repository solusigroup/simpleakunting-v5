<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PinjamanAgunan extends Model
{
    protected $table = 'pinjaman_agunan';
    protected $primaryKey = 'id_agunan';

    protected $fillable = [
        'id_pinjaman',
        'jenis_agunan',
        'nama_agunan',
        'no_dokumen',
        'deskripsi',
        'nilai_taksasi',
        'lokasi_penyimpanan',
        'foto_agunan',
        'status',
        'tanggal_terima',
        'tanggal_kembali',
    ];

    protected $casts = [
        'tanggal_terima' => 'date',
        'tanggal_kembali' => 'date',
        'nilai_taksasi' => 'decimal:2',
    ];

    /**
     * Jenis agunan options
     */
    public static function getJenisAgunanOptions(): array
    {
        return [
            'sertifikat' => 'Sertifikat Tanah/Bangunan',
            'bpkb' => 'BPKB Kendaraan',
            'sk_kerja' => 'SK Kerja/Pengangkatan',
            'deposito' => 'Deposito/Tabungan',
            'lainnya' => 'Lainnya',
        ];
    }

    /**
     * Relasi ke Pinjaman
     */
    public function pinjaman(): BelongsTo
    {
        return $this->belongsTo(Pinjaman::class, 'id_pinjaman', 'id_pinjaman');
    }
}
