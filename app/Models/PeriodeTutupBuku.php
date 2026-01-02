<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PeriodeTutupBuku extends Model
{
    use HasFactory;

    protected $table = 'periode_tutup_buku';

    protected $fillable = [
        'bulan',
        'tahun',
        'tanggal_tutup',
        'user_id',
        'status',
        'keterangan',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'user_id'); // Match migration column
    }
}
