<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturPenjualan extends Model
{
    protected $table = 'retur_penjualan';
    protected $primaryKey = 'id_retur_penjualan';
    protected $fillable = ['id_penjualan', 'id_pelanggan', 'id_jurnal', 'no_retur', 'tanggal', 'total_retur', 'keterangan'];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class, 'id_penjualan');
    }

    public function details()
    {
        return $this->hasMany(ReturPenjualanDetail::class, 'id_retur_penjualan');
    }

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }
}
