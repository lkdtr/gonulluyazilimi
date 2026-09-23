<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: #f8f8f8;">
<head>
    @include('layouts.partials.head')
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T4XWJ3LM"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div id="app">
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ secure_url('/') }}">
                    <!-- {{ config('app.name', 'Laravel') }} -->
                    <img src="/images/lkd-gonullusu.png?v3" alt="Linux Kullanıcıları Derneği Gönüllüsü" style="height: 75px;">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">
                        @auth
                            @include('layouts.partials.menu', ['section' => 'user'])
                        @endauth
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ trans("auth.login") }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ trans(config('app.register_label', 'auth.register')) }}</a>
                                </li>
                            @endif
                        @else

                            @if (in_array((int) Auth::user()->role, [1, 2], true))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.dashboard') }}">{{ trans("panel.manager_operations") }}</a>
                                </li>
                            @endif

                            @include('layouts.partials.account-menu')
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4" style="background-color: #f8f8f8;">
            @yield('content')
        </main>

        <footer style="background-color: #f8f8f8;">
            <div class="container">
                <small>Soru, Şikayet, Önerileriniz için <a href="mailto:gonullu@lkd.org.tr">gonullu@lkd.org.tr</a> adresine eposta gönderebilirsiniz.</small>
                <br/>
                <small>Linux Kullanıcıları Derneği Gönüllü Yazılımı, özgür lisanlara sahip, açık kaynak kodulu yazılımdır. <a href="https://github.com/lkdtr/gonulluyazilimi">Kaynak kodlarına ulaşmak için tıklayın</a></small>
            </div>
        </footer>
    </div>

    @include('layouts.partials.scripts')
</body>
</html>
