<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        {{-- <link rel="shortcut icon" href="{{ asset('') }}" type="image/x-icon"> --}}
        <title inertia>{{ config('app.name', 'AlwaysChat') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- metadata -->
        <link rel="icon" type="image/png" href="/source/favicon.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/source/favicon.png">

        <meta property="og:title" content="AlwaysChat">
        <meta property="og:description" content="Platform perpesanan instan yang aman, cepat, dan modern. Terhubung, berbagi pesan, dan berkomunikasi secara real-time dengan antarmuka yang bersih dan responsif.">
        <meta property="og:image" content="/source/thumbnail.png">
        <meta property="og:url" content="https://AlwaysChat.com/">
        <meta property="og:type" content="website">

        <!-- Metadata untuk Twitter Cards -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="AlwaysChat">
        <meta name="twitter:description" content="Platform perpesanan instan yang aman, cepat, dan modern. Terhubung, berbagi pesan, dan berkomunikasi secara real-time dengan antarmuka yang bersih dan responsif.">
        <meta name="twitter:image" content="/source/thumbnail.png">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
