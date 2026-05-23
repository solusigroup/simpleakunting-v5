<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenjualanPenawaranDetail extends Model
{
    use HasFactory;

    protected $table = 'penjualan_penawaran_detail';
    protected $primaryKey = 'id_detail';
    protected $guarded = ['id_detail'];

    public function penawaran()
    {
        return $this->belongsTo(PenjualanPenawaran::class, 'id_penawaran');
    }

    public function barang()
    {
        return $this->belongsTo(Persediaan::class, 'id_barang');
    }
}
