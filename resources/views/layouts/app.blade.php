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

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
    new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
    j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
    'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
    })(window,document,'script','dataLayer','GTM-T4XWJ3LM');</script>
    <!-- End Google Tag Manager -->

    <x-head.tinymce-config/>
</head>
<body>
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-T4XWJ3LM"
    height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
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

                        @foreach (['user' => 'panel.user_operations', 'admin' => 'panel.manager_operations'] as $section => $title)
                            @php($menuGroups = app(\App\Modules\Menu::class)->groups($section, Auth::user()))
                            @if ($menuGroups)
                                <li class="nav-item dropdown">
                                    <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                        {{ trans($title) }}
                                    </a>
                                    <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                        @foreach ($menuGroups as $items)
                                            @if (! $loop->first)
                                                <hr style="margin: 5px; color: #999;">
                                            @endif
                                            @foreach ($items as $item)
                                                <a class="dropdown-item" href="{{ route($item['route']) }}">{{ __($item['label']) }}</a>
                                            @endforeach
                                        @endforeach
                                    </div>
                                </li>
                            @endif
                        @endforeach

                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }} {{ Auth::user()->surname }}
                                </a>
                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('my-infos') }}">
                                        {{trans('panel.my_infos')}}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('password.change.edit') }}">Parola değiştir</a>
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

        <footer style="background-color: #f8f8f8;">
            <div class="container">
                <small>Soru, Şikayet, Önerileriniz için <a href="mailto:gonullu@lkd.org.tr">gonullu@lkd.org.tr</a> adresine eposta gönderebilirsiniz.</small>
                <br/>
                <small>Linux Kullanıcıları Derneği Gönüllü Yazılımı, özgür lisanlara sahip, açık kaynak kodulu yazılımdır. <a href="https://github.com/lkdtr/gonulluyazilimi">Kaynak kodlarına ulaşmak için tıklayın</a></small>
            </div>
        </footer>
    </div>

    <script>
    tinymce.init({
      selector: '.wysiwyg',
      plugins: 'ai tinycomments mentions anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount checklist mediaembed casechange export formatpainter pageembed permanentpen footnotes advtemplate advtable advcode editimage tableofcontents mergetags powerpaste tinymcespellchecker autocorrect a11ychecker typography inlinecss',
      toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | align lineheight | tinycomments | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
      tinycomments_mode: 'embedded',
      tinycomments_author: 'Author name',
      mergetags_list: [
        { value: 'First.Name', title: 'First Name' },
        { value: 'Email', title: 'Email' },
      ],
      ai_request: (request, respondWith) => respondWith.string(() => Promise.reject("See docs to implement AI Assistant"))
    });
  </script>

    @include('layouts.partials.svg-icons')
</body>
</html>
