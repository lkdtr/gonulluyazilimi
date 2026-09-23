<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: #f8f8f8;">
<head>
    @include('layouts.partials.head')
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
            <div class="container-fluid">
                <a class="navbar-brand" href="{{ route('admin.dashboard') }}">{{ trans("panel.manager_operations") }}</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="adminNavbar">
                    <ul class="navbar-nav me-auto">
                        @include('layouts.partials.menu', ['section' => 'admin'])
                    </ul>

                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Siteye dön</a>
                        </li>
                        @include('layouts.partials.account-menu')
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4" style="background-color: #f8f8f8;">
            @yield('content')
        </main>
    </div>

    @include('layouts.partials.scripts')
</body>
</html>
