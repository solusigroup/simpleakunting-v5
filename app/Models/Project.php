<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $table = 'projects';
    protected $primaryKey = 'id_project';

    protected $fillable = [
        'kode_project',
        'nama_project',
        'id_unit_usaha',
        'status',
        'keterangan',
    ];

    public function unitUsaha()
    {
        return $this->belongsTo(UnitUsaha::class, 'id_unit_usaha');
    }

    /**
     * Scope untuk proyek yang aktif saja.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'Aktif');
    }
}
