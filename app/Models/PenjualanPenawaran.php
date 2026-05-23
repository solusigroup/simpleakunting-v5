<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class PenjualanPenawaran extends Model
{
    use HasFactory, LogsActivity, \App\Traits\HasCabang;

    protected $table = 'penjualan_penawaran';
    protected $primaryKey = 'id_penawaran';
    protected $guarded = ['id_penawaran'];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function details()
    {
        return $this->hasMany(PenjualanPenawaranDetail::class, 'id_penawaran');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function unitUsaha()
    {
        return $this->belongsTo(UnitUsaha::class, 'id_unit_usaha');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }
}
