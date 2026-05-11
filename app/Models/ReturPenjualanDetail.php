<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturPenjualanDetail extends Model
{
    protected $table = 'retur_penjualan_detail';
    protected $primaryKey = 'id_detail';
    protected $fillable = ['id_retur_penjualan', 'id_barang', 'kuantitas', 'harga', 'subtotal'];

    public function retur()
    {
        return $this->belongsTo(ReturPenjualan::class, 'id_retur_penjualan');
    }

    public function barang()
    {
        return $this->belongsTo(Persediaan::class, 'id_barang');
    }
}
