<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditTrail extends Model
{
    protected $table = 'audit_trails';
    protected $guarded = ['id'];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Log an audit trail entry
     */
    public static function log(string $action, string $description, ?Model $model = null, ?array $oldValues = null, ?array $newValues = null): self
    {
        $user = auth()->user();

        return static::create([
            'user_id' => $user?->id,
            'user_name' => $user?->nama_user ?? 'System',
            'action' => $action,
            'model_type' => $model ? get_class($model) : null,
            'model_id' => $model ? $model->getKey() : null,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
        ]);
    }

    /**
     * Get action badge color
     */
    public function getActionBadgeAttribute(): string
    {
        return match ($this->action) {
            'created' => 'success',
            'updated' => 'warning',
            'deleted' => 'danger',
            'login' => 'info',
            'logout' => 'secondary',
            default => 'primary',
        };
    }

    /**
     * Get short model name (without namespace)
     */
    public function getModelNameAttribute(): string
    {
        if (!$this->model_type) return '-';
        return class_basename($this->model_type);
    }
}
