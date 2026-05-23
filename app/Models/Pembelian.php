<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Pembelian extends Model
{
    use HasFactory, LogsActivity, \App\Traits\HasCabang;

    protected $table = 'pembelian';
    protected $primaryKey = 'id_pembelian';
    protected $guarded = ['id_pembelian'];

    public function pemasok()
    {
        return $this->belongsTo(Pemasok::class, 'id_pemasok');
    }

    public function details()
    {
        return $this->hasMany(PembelianDetail::class, 'id_pembelian');
    }

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }

    public function getNoFakturAttribute()
    {
        return $this->attributes['no_faktur_pembelian'];
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
