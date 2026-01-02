<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bom extends Model
{
    use HasFactory;

    protected $table = 'bom';

    protected $fillable = [
        'kode_bom',
        'nama_bom',
        'barang_jadi_id',
        'kuantitas_hasil',
        'deskripsi',
    ];

    public function barangJadi()
    {
        return $this->belongsTo(Persediaan::class, 'barang_jadi_id', 'id_barang');
    }

    public function details()
    {
        return $this->hasMany(BomDetail::class, 'bom_id');
    }
}
