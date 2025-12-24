<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovalHistory extends Model
{
    protected $table = 'approval_history';

    protected $fillable = [
        'module',
        'reference_id',
        'level',
        'user_id',
        'action',
        'notes',
    ];

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Get action label
     */
    public function getActionLabelAttribute(): string
    {
        return match($this->action) {
            'submit' => 'Disubmit',
            'approve' => 'Disetujui',
            'reject' => 'Ditolak',
            'return' => 'Dikembalikan',
            default => ucfirst($this->action),
        };
    }

    /**
     * Get action color
     */
    public function getActionColorAttribute(): string
    {
        return match($this->action) {
            'submit' => 'info',
            'approve' => 'success',
            'reject' => 'danger',
            'return' => 'warning',
            default => 'secondary',
        };
    }
}
