<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    @include('layouts.partials.head')
</head>
<body>
    @if ($gtmId = $organization->get('gtm_container_id'))
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id={{ $gtmId }}"
        height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
    @endif

    <div class="page" id="app">
        <header class="navbar navbar-expand-md d-print-none">
            <div class="container-xl">
                @auth
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                @endauth

                <a class="navbar-brand pe-0 pe-md-3" href="{{ secure_url('/') }}">
                    @if ($organization->logoUrl())
                        <img src="{{ $organization->logoUrl() }}" alt="{{ $organization->name() }}" class="navbar-brand-image">
                    @else
                        <span class="fw-bold text-reset">{{ $organization->shortName() }}</span>
                    @endif
                </a>

                <div class="navbar-nav flex-row order-md-last align-items-center gap-2">
                    @guest
                        @if (Route::has('login'))
                            <a class="btn btn-ghost-secondary" href="{{ route('login') }}">{{ trans("auth.login") }}</a>
                        @endif
                        @if (Route::has('register'))
                            <a class="btn btn-primary" href="{{ route('register') }}">{{ trans(config('app.register_label', 'auth.register')) }}</a>
                        @endif
                    @else
                        @if (in_array(Auth::user()->accessLevel(), [1, 2], true))
                            <a class="btn btn-outline-secondary d-none d-sm-inline-flex" href="{{ route('admin.dashboard') }}">
                                <i class="ti ti-settings icon"></i>{{ trans("panel.manager_operations") }}
                            </a>
                        @endif
                        @include('layouts.partials.account-menu')
                    @endguest
                </div>
            </div>
        </header>

        @auth
            <header class="navbar-expand-md">
                <div class="collapse navbar-collapse" id="navbar-menu">
                    <div class="navbar">
                        <div class="container-xl">
                            <ul class="navbar-nav">
                                @include('layouts.partials.menu', ['section' => 'user'])
                                @if (in_array(Auth::user()->accessLevel(), [1, 2], true))
                                    <li class="nav-item d-sm-none">
                                        <a class="nav-link" href="{{ route('admin.dashboard') }}">
                                            <span class="nav-link-icon"><i class="ti ti-settings icon"></i></span>
                                            <span class="nav-link-title">{{ trans("panel.manager_operations") }}</span>
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </header>
        @endauth

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
