<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    use HasFactory;

    protected $table = 'produksi';

    protected $fillable = [
        'no_produksi',
        'tanggal',
        'bom_id',
        'id_cabang',
        'kuantitas_produksi',
        'status',
        'keterangan',
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class, 'bom_id');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function details()
    {
        return $this->hasMany(ProduksiDetail::class, 'produksi_id');
    }
}
