<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persediaan extends Model
{
    use HasFactory;

    protected $table = 'master_persediaan';
    protected $primaryKey = 'id_barang';
    protected $guarded = ['id_barang'];

    public function kartuStok()
    {
        return $this->hasMany(KartuStok::class, 'id_barang');
    }

    public function penjualanDetails()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_barang');
    }

    public function pembelianDetails()
    {
        return $this->hasMany(PembelianDetail::class, 'id_barang');
    }
}
