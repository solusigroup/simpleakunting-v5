<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiDetail extends Model
{
    use HasFactory;

    protected $table = 'produksi_detail';

    protected $fillable = [
        'produksi_id',
        'material_id',
        'kuantitas_digunakan',
        'biaya_satuan',
        'total_biaya',
    ];

    public function production()
    {
        return $this->belongsTo(Produksi::class, 'produksi_id');
    }

    public function material()
    {
        return $this->belongsTo(Persediaan::class, 'material_id', 'id_barang');
    }
}
