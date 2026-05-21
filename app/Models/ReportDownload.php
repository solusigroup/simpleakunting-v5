<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportDownload extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_laporan',
        'tipe',
        'params',
        'status',
        'file_path',
        'error_message',
        'created_by'
    ];
}
