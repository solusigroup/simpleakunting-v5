<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BomDetail extends Model
{
    use HasFactory;

    protected $table = 'bom_detail';

    protected $fillable = [
        'bom_id',
        'material_id',
        'kuantitas',
        'satuan',
    ];

    public function bom()
    {
        return $this->belongsTo(Bom::class, 'bom_id');
    }

    public function material()
    {
        return $this->belongsTo(Persediaan::class, 'material_id', 'id_barang');
    }
}
