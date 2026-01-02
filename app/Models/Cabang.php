<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    protected $table = 'cabang';

    protected $fillable = [
        'kode_cabang',
        'nama_cabang',
        'alamat',
        'telepon',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'id_cabang');
    }

    public function inventories()
    {
        return $this->hasMany(Persediaan::class, 'id_cabang', 'id_barang'); // Note: This might need a pivot table or adjustment if inventory is strictly per branch. For now assuming simple relation or field.
        // Actually, master_persediaan has id_cabang, so it's one-to-many.
    }
}
