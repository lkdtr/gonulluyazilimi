<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-navbar-position="vertical">
<head>
    @include('layouts.partials.head')
</head>
<body>
    <div class="page" id="app">
        <aside class="navbar navbar-vertical navbar-expand-lg" data-bs-theme="dark">
            <div class="container-fluid">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#sidebar-menu" aria-controls="sidebar-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="navbar-brand navbar-brand-autodark">
                    <a href="{{ route('admin.dashboard') }}" class="text-reset text-decoration-none">
                        <i class="ti ti-settings icon me-1"></i>{{ $organization->shortName() }} Yönetim
                    </a>
                </div>

                <div class="navbar-nav flex-row d-lg-none">
                    @include('layouts.partials.account-menu')
                </div>

                <div class="collapse navbar-collapse" id="sidebar-menu">
                    <ul class="navbar-nav pt-lg-3">
                        @include('layouts.partials.menu', ['section' => 'admin', 'vertical' => true])

                        <li class="nav-item mt-lg-3">
                            <a class="nav-link" href="{{ route('home') }}">
                                <span class="nav-link-icon"><i class="ti ti-arrow-back-up icon"></i></span>
                                <span class="nav-link-title">Siteye dön</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </aside>

        <header class="navbar navbar-expand-md d-none d-lg-flex d-print-none">
            <div class="container-xl">
                <div class="navbar-nav flex-row order-md-last">
                    @include('layouts.partials.account-menu')
                </div>
                <div class="text-secondary">{{ trans("panel.manager_operations") }}</div>
            </div>
        </header>

        <div class="page-wrapper">
            <div class="page-body">
                @yield('content')
            </div>

            @include('layouts.partials.footer')
        </div>
    </div>

    @include('layouts.partials.scripts')
</body>
</html>
