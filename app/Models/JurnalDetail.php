<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JurnalDetail extends Model
{
    use HasFactory;

    protected $table = 'jurnal_detail';
    protected $primaryKey = 'id_detail';
    protected $guarded = ['id_detail'];

    protected static function booted()
    {
        static::addGlobalScope('approved_only', function ($builder) {
            if (\App\Models\Jurnal::$applyApprovalFilter) {
                $builder->whereHas('jurnal', function ($q) {
                    $q->where('is_approved', 1);
                });
            }
        });
    }

    public function jurnal()
    {
        return $this->belongsTo(Jurnal::class, 'id_jurnal');
    }

    public function akun()
    {
        return $this->belongsTo(Akun::class, 'kode_akun');
    }
}
