<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReturPembelian extends Model
{
    protected $table = 'retur_pembelian';
    protected $primaryKey = 'id_retur_pembelian';
    protected $fillable = ['id_pembelian', 'id_pemasok', 'id_jurnal', 'no_retur', 'tanggal', 'total_retur', 'keterangan'];

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok');
    }

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class, 'id_pembelian');
    }

    public function details()
    {
        return $this->hasMany(ReturPembelianDetail::class, 'id_retur_pembelian');
    }

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }
}
