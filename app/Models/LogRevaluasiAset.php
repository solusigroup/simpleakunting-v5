<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogRevaluasiAset extends Model
{
    use HasFactory;

    protected $table = 'log_revaluasi_aset';

    protected $fillable = [
        'aset_biologis_id',
        'tanggal_revaluasi',
        'nilai_buku_sebelum',
        'nilai_wajar_baru',
        'selisih_nilai',
        'keterangan',
    ];

    public function asetBiologis()
    {
        return $this->belongsTo(AsetBiologis::class, 'aset_biologis_id');
    }
}
