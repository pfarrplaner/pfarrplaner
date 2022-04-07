<!DOCTYPE html>
<html lang="de" translate="no">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="google" content="notranslate">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', '') :: {{ config('app.name', 'Pfarrplaner') }}</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/MaterialDesign-Webfont/6.5.95/css/materialdesignicons.min.css" integrity="sha512-Zw6ER2h5+Zjtrej6afEKgS8G5kehmDAHYp9M2xf38MPmpUWX39VrYmdGtCrDQbdLQrTnBVT8/gcNhgS4XPgvEg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- Theme style -->
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('adminlte/css/adminlte.min.css') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.css" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css"
          rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css"
          rel="stylesheet"/>
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.1/css/tempusdominus-bootstrap-4.min.css"/>
    <link rel="stylesheet" type="text/css"
          href="https://cdnjs.cloudflare.com/ajax/libs/jquery-date-range-picker/0.20.0/daterangepicker.min.css"/>
    <link href="{{ asset('css/pfarrplaner.css') }}?v=20191207162200" rel="stylesheet">

    <link href="{{ mix('/css/app.css') }}&v={{ json_decode(file_get_contents(base_path('package.json')), true)['version'] }}" rel="stylesheet">
    @routes()
    <script src="{{ mix('/js/inertia-app.js') }}&v={{ json_decode(file_get_contents(base_path('package.json')), true)['version'] }}" defer></script>
@yield('styles', '')

<!-- favicons -->
    <link rel="apple-touch-icon" sizes="180x180" href="/img/favicons/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/img/favicons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/img/favicons/favicon-16x16.png">
    <link rel="manifest" href="/img/favicons/site.webmanifest">
    <link rel="mask-icon" href="/img/favicons/safari-pinned-tab.svg" color="#5bbad5">
    <link rel="shortcut icon" href="/img/favicons/favicon.ico">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="msapplication-config" content="/img/favicons/browserconfig.xml">
    <meta name="theme-color" content="#ffffff">
</head>
<body
    class="notranslate hold-transition sidebar-mini sidebar-collapse">
    @inertia
<!-- ./wrapper -->

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->
<script>
    @auth
        window.Laravel = {!! json_encode([
       'csrfToken' => csrf_token(),
       'apiToken' => Auth::user()->api_token ?? null,
       ]); !!};
    @endauth
        @guest
        window.Laravel = {};
    @endguest

        window.Laravel.loggedIn = {{ json_encode(!Auth::guest()) }};
    window.Laravel.timeout = {{ (config('session.lifetime')*60000)-30000 }};
    window.Laravel.expires = new Date('{!! \Carbon\Carbon::now()->addMinutes(config('session.lifetime'))->toIso8601String() !!}');
    @auth
    window.Laravel.permissions = {!!  json_encode(Auth::user()->getAllPermissions()->pluck('name')) !!};
    @endauth
    @guest
        window.Laravel.permissions = [];
    @endguest

    window.Laravel.assetUrl = '{{ asset('') }}';

    window.setTimeout(function () {
        if (window.Laravel.loggedIn) {
            location.href = '{!! route('logout') !!}';
        } else {
            console.log('Refreshing window to update crsf token.');
            location.reload();
        }
    }, window.Laravel.timeout);
</script>
<!-- other libraries -->
@yield('scripts')
@if(env('MATOMO_SITE') >0)
    <script>
        var _paq = window._paq || [];
        /* tracker methods like "setCustomDimension" should be called before "trackPageView" */
        _paq.push(['trackPageView']);
        _paq.push(['enableLinkTracking']);
        (function () {
            var u = "//matomo.pfarrplaner.de/";
            _paq.push(['setTrackerUrl', u + 'matomo.php']);
            _paq.push(['setSiteId', '{{ env('MATOMO_SITE') }}']);
            var d = document, g = d.createElement('script'), s = d.getElementsByTagName('script')[0];
            g.type = 'text/javascript';
            g.async = true;
            g.defer = true;
            g.src = u + 'matomo.js';
            s.parentNode.insertBefore(g, s);
        })();
    </script>
@endif
</body>
</html>
