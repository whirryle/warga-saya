<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />

    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{{ config('app.name') }}</title>

    <style>
        [x-cloak] {
            display: none !important;
        }

        select {
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            /* background-image: none !important; */
            background-repeat: no-repeat;
            background-position: right 0.5rem center;
        }

        /* Optional: jika masih ada ::after atau ::before dari plugin */
        select::before,
        select::after {
            content: none !important;
            display: none !important;
        }
    </style>

    @filamentStyles
    {{-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> <!-- Jika menggunakan Mix --> --}}

    <!-- Di app.blade.php -->
    {{-- <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script> --}}
    <link href="{{ asset('css/filament.css') }}" rel="stylesheet">

    {{-- Load Tailwind CSS via Vite (handles both dev server and production build) --}}
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

</head>

<body class="antialiased">
    {{ $slot }}

    @livewire('notifications')

</body>

</html>
