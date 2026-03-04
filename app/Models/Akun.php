<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Akun extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'akun';
    protected $primaryKey = 'kode_akun';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = ['kode_akun', 'nama_akun', 'tipe_akun', 'saldo_normal', 'saldo_awal', 'account_category'];
}
