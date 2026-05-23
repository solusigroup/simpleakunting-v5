<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianRfqDetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_rfq_detail';
    protected $primaryKey = 'id_detail';
    protected $guarded = ['id_detail'];

    public function rfq()
    {
        return $this->belongsTo(PembelianRfq::class, 'id_rfq');
    }

    public function barang()
    {
        return $this->belongsTo(Persediaan::class, 'id_barang');
    }
}
