<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'WinBoard') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/scss/app.scss', 'resources/js/app.js'])
</head>
<body>
    <div class="app-shell">
        @include('layouts.navigation')

        @isset($header)
            <div class="page-header">
                <div class="page-header__inner">
                    {{ $header }}
                </div>
            </div>
        @endisset

        <main class="page-content">
            {{ $slot }}
        </main>
    </div>
</body>
</html>
