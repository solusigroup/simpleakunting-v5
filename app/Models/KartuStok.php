<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KartuStok extends Model
{
    use HasFactory;

    protected $table = 'kartu_stok';
    protected $primaryKey = 'id_kartu';
    protected $guarded = ['id_kartu'];

    public function barang()
    {
        return $this->belongsTo(Persediaan::class, 'id_barang');
    }
}
