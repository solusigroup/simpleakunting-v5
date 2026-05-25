<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Penjualan extends Model
{
    use HasFactory, LogsActivity, \App\Traits\HasCabang, \App\Traits\ClearsDashboardCache;

    protected $table = 'penjualan';
    protected $primaryKey = 'id_penjualan';
    protected $guarded = ['id_penjualan'];

    public function pelanggan()
    {
        return $this->belongsTo(Pelanggan::class, 'id_pelanggan');
    }

    public function details()
    {
        return $this->hasMany(PenjualanDetail::class, 'id_penjualan');
    }

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }

    public function posSession()
    {
        return $this->belongsTo(PosSession::class, 'id_pos_session');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }
}
