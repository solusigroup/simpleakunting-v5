<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Jurnal extends Model
{
    use HasFactory, LogsActivity, \App\Traits\HasCabang;

    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal';
    protected $guarded = ['id_jurnal'];

    public function details()
    {
        return $this->hasMany(JurnalDetail::class, 'id_jurnal');
    }
}
