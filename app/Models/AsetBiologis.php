<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AsetBiologis extends Model
{
    use HasFactory;

    protected $table = 'aset_biologis';

    protected $fillable = [
        'kode_aset',
        'nama_aset',
        'jenis',
        'tanggal_perolehan',
        'umur_bulan',
        'lokasi',
        'nilai_perolehan',
        'nilai_wajar',
        'estimasi_biaya_jual',
        'id_cabang',
        'status',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function revaluasiLogs()
    {
        return $this->hasMany(LogRevaluasiAset::class, 'aset_biologis_id');
    }
}
