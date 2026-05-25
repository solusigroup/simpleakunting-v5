<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosSession extends Model
{
    use HasFactory, \App\Traits\ClearsDashboardCache;

    protected $table = 'pos_sessions';
    protected $guarded = ['id'];

    protected $casts = [
        'saldo_awal' => 'decimal:2',
        'saldo_akhir' => 'decimal:2',
        'total_penjualan' => 'decimal:2',
        'total_pembelian' => 'decimal:2',
        'selisih' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'id_cabang');
    }

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class, 'id_pos_session');
    }

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class, 'id_pos_session');
    }

    public function isOpen(): bool
    {
        return is_null($this->closed_at);
    }
}
