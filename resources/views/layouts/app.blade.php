<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="background-color: #f8f8f8;">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title')</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js?v=').time() }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css?v=').time() }}" rel="stylesheet">

    <script type="text/javascript">
		var _globalToken = {!! json_encode(array('_token'=> csrf_token())) !!}

        @if(env('APP_ENV')!="local")
            if (location.protocol !== 'https:') {
                location.replace(`https:${location.href.substring(location.protocol.length)}`);
            }
        @endif
	</script>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-FH9QSFK0HF"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-FH9QSFK0HF');
    </script>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ secure_url('/') }}">
                    <!-- {{ config('app.name', 'Laravel') }} -->
                    <img src="https://www.lkd.org.tr/wp-content/uploads/2022/03/cropped-LKD_logo.gif" style="height: 50px;">
                    <span style="font-size: 20px;"> Gönüllü Sistemi</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

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
                                    <a class="nav-link" href="{{ route('register') }}">{{ trans("auth.become_a_volunteer") }}</a>
                                </li>
                            @endif
                        @else

                        <li class="nav-item dropdown">
                            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                {{ trans("panel.user_operations") }}
                            </a>
                            <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="{{ route('email-redirects') }}">
                                    {{trans('panel.email_forwarding')}}
                                </a>
                                <hr style="margin: 5px; color: #999;">
                                <a class="dropdown-item" href="{{ route('create-seminar-request') }}">
                                    {{ trans("panel.create_seminar_request") }}
                                </a>
                                <hr style="margin: 5px; color: #999;">
                                <a class="dropdown-item" href="{{ route('create-reference-request') }}">
                                    {{ trans("panel.create_reference_request") }}
                                </a>

                            </div>
                        </li>

                        @if( (Auth::user()->role==1) || (Auth::user()->role==2) )
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ trans("panel.manager_operations") }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('users') }}">
                                        {{ trans("panel.users") }}
                                    </a>
                                    <hr style="margin: 5px; color: #999;">
                                    <a class="dropdown-item" href="{{ route('process-logs') }}">
                                        {{ trans("panel.process_logs") }}
                                    </a>
                                    <hr style="margin: 5px; color: #999;">
                                    <a class="dropdown-item" href="{{ route('announcements') }}">
                                        {{ trans("panel.announcements") }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('new-announcement') }}">
                                        {{ trans("panel.new_announcement") }}
                                    </a>
                                    <hr style="margin: 5px; color: #999;">
                                    <a class="dropdown-item" href="{{ route('seminar-subjects') }}">
                                        {{ trans("panel.seminar_subjects") }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('new-seminar-subject') }}">
                                        {{ trans("panel.new_seminar_subject") }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('seminar-requests') }}">
                                        {{ trans("panel.seminar_requests") }}
                                    </a>
                                    <hr style="margin: 5px; color: #999;">
                                    <a class="dropdown-item" href="{{ route('reference-requests') }}">
                                        {{ trans("panel.reference_requests") }}
                                    </a>
                                </div>
                            </li>
                        @endif

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} {{ Auth::user()->surname }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('my-infos') }}">
                                        {{trans('panel.my_infos')}}
                                    </a>
                                    <hr style="margin: 5px; color: #999;">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ trans("auth.logout") }}
                                    </a>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4" style="background-color: #f8f8f8;">
            @yield('content')
        </main>
    </div>

    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
        <symbol id="check-circle-fill" viewBox="0 0 16 16">
            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
        </symbol>
        <symbol id="info-fill" viewBox="0 0 16 16">
            <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
        </symbol>
        <symbol id="exclamation-triangle-fill" viewBox="0 0 16 16">
            <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
        </symbol>
    </svg>
</body>
</html>
