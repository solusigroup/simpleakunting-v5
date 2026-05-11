<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturPembelianDetail extends Model
{
    protected $table = 'retur_pembelian_detail';
    protected $primaryKey = 'id_detail';
    protected $fillable = ['id_retur_pembelian', 'id_barang', 'kuantitas', 'harga', 'subtotal'];

    public function retur()
    {
        return $this->belongsTo(ReturPembelian::class, 'id_retur_pembelian');
    }

    public function barang()
    {
        return $this->belongsTo(Persediaan::class, 'id_barang');
    }
}
