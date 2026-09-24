    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $organization->name() }} - @yield('title')</title>
    @if ($organization->faviconUrl())
        <link rel="icon" href="{{ $organization->faviconUrl() }}">
    @endif

    <!-- Scripts -->
    <script src="{{ asset('js/app.js?v=').time() }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/app.css?v=').time() }}" rel="stylesheet">
    @if ($theme = $organization->themeCss())
        <style>{!! $theme !!}</style>
    @endif

    <script type="text/javascript">
		var _globalToken = {!! json_encode(array('_token'=> csrf_token())) !!}

        @if(env('APP_ENV')!="local")
            if (location.protocol !== 'https:') {
                location.replace(`https:${location.href.substring(location.protocol.length)}`);
            }
        @endif
	</script>

    @if ($gaId = $organization->get('ga_measurement_id'))
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gaId }}"></script>
        <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @json($gaId));
        </script>
    @endif

    @if ($gtmId = $organization->get('gtm_container_id'))
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer',@json($gtmId));</script>
        <!-- End Google Tag Manager -->
    @endif

    <x-head.tinymce-config/>
