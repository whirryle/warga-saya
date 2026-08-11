<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Pages\PaymentTracker;
use Filament\PanelProvider;
// use App\Livewire\ShowCivilians;
use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Filament\Support\Assets\Css;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentAsset;
// use App\Filament\Pages\LaporanStatusPernikahan;
use App\Http\Livewire\LaporanStatusPernikahan;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Tambahkan jQuery dan Select2
        FilamentAsset::register([
            Js::make('jquery', 'https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js'), // jQuery

        ]);

        return $panel

            ->default()
            ->id('admin')
            ->path('')
            ->login()
            ->registration()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->darkMode(true)
            ->viteTheme('resources/css/app.css')
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->navigationGroups([
                'Master Data',
                'Kategori',
                'Iuran',
                'Laporan',
            ])
            ->spa()
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
