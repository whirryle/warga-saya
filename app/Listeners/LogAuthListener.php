<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Failed;
use Spatie\Activitylog\Models\Activity;

class LogAuthListener
{
    public function handle(Login|Logout|Registered|Failed $event): void
    {
        $user = $event->user ?? null;

        $description = match (true) {
            $event instanceof Login => 'Login ke panel',
            $event instanceof Logout => 'Logout dari panel',
            $event instanceof Registered => 'Mendaftarkan akun baru',
            $event instanceof Failed => 'Percobaan login gagal',
            default => 'Aktivitas autentikasi',
        };

        $properties = [];

        if ($event instanceof Failed) {
            $properties['email'] = $event->credentials['email'] ?? null;
        }

        $activity = activity()
            ->useLog('Autentikasi')
            ->withProperties($properties)
            ->event(class_basename($event))
            ->tap(function (Activity $activity) use ($user) {
                $activity->role = $user?->role;
                $activity->ip_address = request()->ip();
                $activity->user_agent = substr((string) request()->userAgent(), 0, 500);
            });

        if ($user) {
            $activity->causedBy($user);
        }

        $activity->log($description);
    }
}
