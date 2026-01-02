<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IkhtisarLabaRugi extends Model
{
    use HasFactory;

    protected $table = 'ikhtisar_laba_rugi';

    protected $fillable = [
        'periode_id',
        'total_pendapatan',
        'total_beban',
        'laba_rugi_bersih',
    ];

    public function periode()
    {
        return $this->belongsTo(PeriodeTutupBuku::class, 'periode_id');
    }
}
