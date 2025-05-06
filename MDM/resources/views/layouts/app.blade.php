<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'MDM System') }}</title>

    <!-- Bootstrap CSS -->
    <link href="{{ mix('css/app.css') }}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">



</head>

<body>
    @include('layouts.navigation')

    <main class="container mt-4">
        @if (isset($header))
        <div class="bg-light p-3 mb-4 rounded shadow-sm">
            <h2 class="h4">{{ $header }}</h2>
        </div>
        @endif

        {{ $slot }}
    </main>

    <!-- Bootstrap JS -->
    <script src="{{ mix('js/app.js') }}" defer></script>
</body>

</html>