<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Str;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            if (!auth()->check()) return;
            $class = class_basename($model);
            $action = strtolower(Str::snake($class)) . '.created';
            $desc = "Created a new {$class} record (#{$model->getKey()})";
            ActivityLog::record($action, $desc);
        });

        static::updated(function ($model) {
            if (!auth()->check()) return;
            $class = class_basename($model);
            $action = strtolower(Str::snake($class)) . '.updated';
            
            // Optionally, get changed attributes
            $changes = $model->getChanges();
            // remove updated_at
            unset($changes['updated_at']);

            $desc = "Updated {$class} record (#{$model->getKey()})";
            if (!empty($changes)) {
                $keys = implode(', ', array_keys($changes));
                $desc .= ". Changed: {$keys}";
            }

            ActivityLog::record($action, $desc);
        });

        static::deleted(function ($model) {
            if (!auth()->check()) return;
            $class = class_basename($model);
            $action = strtolower(Str::snake($class)) . '.deleted';
            $desc = "Deleted {$class} record (#{$model->getKey()})";
            ActivityLog::record($action, $desc);
        });
    }
}
