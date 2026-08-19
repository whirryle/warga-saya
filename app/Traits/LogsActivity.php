<?php

namespace App\Traits;

use Spatie\Activitylog\Traits\LogsActivity as SpatieLogsActivity;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\LogOptions;

trait LogsActivity
{
    use SpatieLogsActivity;

    protected function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->useLogName($this->activityLogName());
    }

    public function tapActivity(Activity $activity, string $eventName)
    {
        $user = auth()->user();

        $activity->role = $user?->role;
        $activity->ip_address = request()->ip();
        $activity->user_agent = substr((string) request()->userAgent(), 0, 500);

        // Privasi: jangan simpan nilai atribut (NIK, alamat, nomor HP, nominal) di properties.
        $activity->properties = null;
    }

    public function getDescriptionForEvent(string $eventName): string
    {
        $label = $this->activityLogName();

        return match ($eventName) {
            'created' => "Menambahkan {$label} baru",
            'updated' => "Mengubah {$label}",
            'deleted' => "Menghapus {$label}",
            default => "{$label} {$eventName}",
        };
    }

    protected function activityLogName(): string
    {
        return strtolower(class_basename($this));
    }
}
