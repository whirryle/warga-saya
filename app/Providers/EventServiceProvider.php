<?php

namespace App\Providers;

use App\Listeners\LogAuthListener;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Failed;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Login::class => [
            LogAuthListener::class,
        ],
        Logout::class => [
            LogAuthListener::class,
        ],
        Registered::class => [
            LogAuthListener::class,
        ],
        Failed::class => [
            LogAuthListener::class,
        ],
    ];

    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
