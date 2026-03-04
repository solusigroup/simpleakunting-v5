<?php

namespace App\Traits;

use App\Models\AuditTrail;

/**
 * Auto-log created, updated, deleted events via Eloquent model events.
 * Add `use LogsActivity;` to any model you want to track.
 */
trait LogsActivity
{
    public static function bootLogsActivity(): void
    {
        static::created(function ($model) {
            AuditTrail::log(
                'created',
                class_basename($model) . ' dibuat: ' . ($model->getKey()),
                $model,
                null,
                $model->getAttributes()
            );
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) return;

            $original = collect($model->getOriginal())
                ->only(array_keys($dirty))
                ->toArray();

            AuditTrail::log(
                'updated',
                class_basename($model) . ' diubah: ' . ($model->getKey()),
                $model,
                $original,
                $dirty
            );
        });

        static::deleted(function ($model) {
            AuditTrail::log(
                'deleted',
                class_basename($model) . ' dihapus: ' . ($model->getKey()),
                $model,
                $model->getAttributes(),
                null
            );
        });
    }
}
