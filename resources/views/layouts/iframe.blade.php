<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: #f8f8f8;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Seminer Talebi')</title>
    <script src="{{ asset('js/app.js?v=').time() }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script type="text/javascript">
        var _globalToken = {!! json_encode(array('_token'=> csrf_token())) !!}
    </script>
    <style>
        body { margin: 0; }
        main .container { width: 100%; max-width: none; padding-right: 12px; padding-left: 12px; }
    </style>
</head>
<body>
    <main class="py-4" style="background-color: #f8f8f8; min-height: 100vh;">
        @yield('content')
    </main>

    @include('layouts.partials.svg-icons')
</body>
</html>
