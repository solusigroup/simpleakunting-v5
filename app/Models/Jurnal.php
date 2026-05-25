<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Jurnal extends Model
{
    use HasFactory, LogsActivity, \App\Traits\HasCabang, \App\Traits\ClearsDashboardCache;

    public static $applyApprovalFilter = false;

    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal';
    protected $guarded = ['id_jurnal'];

    protected static function booted()
    {
        static::addGlobalScope('approved_only', function ($builder) {
            if (static::$applyApprovalFilter) {
                $builder->where('is_approved', 1);
            }
        });
    }

    public function details()
    {
        return $this->hasMany(JurnalDetail::class, 'id_jurnal');
    }

    public function project()
    {
        return $this->belongsTo(Project::class, 'id_project');
    }
}
