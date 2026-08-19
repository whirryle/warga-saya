<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class LogAktivitas extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static string $view = 'filament.pages.log-aktivitas';
    protected static ?string $navigationLabel = 'Log Aktivitas';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?string $title = 'Log Aktivitas';

    public static function canView(): bool
    {
        return auth()->user()?->isSuperAdmin() ?? false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canView();
    }
}