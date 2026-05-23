<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class PembelianRfq extends Model
{
    use HasFactory, LogsActivity, \App\Traits\HasCabang;

    protected $table = 'pembelian_rfq';
    protected $primaryKey = 'id_rfq';
    protected $guarded = ['id_rfq'];

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok');
    }

    public function details()
    {
        return $this->hasMany(PembelianRfqDetail::class, 'id_rfq');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function unitUsaha()
    {
        return $this->belongsTo(UnitUsaha::class, 'id_unit_usaha');
    }
}
