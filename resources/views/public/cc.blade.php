<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Kinderkirche {{ $city->name }} :: Dienstplan Online</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.js"
            integrity="sha256-59IZ5dbLyByZgSsRE3Z0TjDuX7e1AiqW5bZ8Bg50dsU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/de.js"
            integrity="sha256-wUoStqxFxc33Uz7o+njPIobHc4HJjMQqMXNRDy7X3ps=" crossorigin="anonymous"></script>
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Styles -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css" rel="stylesheet"/>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dienstplan.css') }}" rel="stylesheet">


</head>
<body>
<div id="app">
    <main class="py-1">
        @component('components.container')
            <h1>Kinderkirche {{ $city->name }}</h1>
            <p><b>Von {{ $start->format('d.m.Y') }} bis {{ $end->format('d.m.Y') }}</b></p>
            <a class="btn btn-primary btn-lg" href="{{ route('cc-public-pdf', $city->name) }}"><span class="fa fa-file-pdf"></span> Als PDF herunterladen</a>
            <hr/>

            @if($count)

            <table class="table table-fluid table-striped">
                <thead></thead>
                <tbody>
                @foreach ($services as $dayServices)
                    @foreach ($dayServices as $service)
                        <tr>
                            <td valign="top" width="10%">{{ $service->day->date->format('d.m.Y') }}</td>
                            <td valign="top" width="10%">{{ strftime('%H:%M', strtotime($service->time)) }} Uhr</td>
                            <td valign="top" width="20%" @if($service->hasNonStandardCCLocation()) style="color: red;" @endif>{{ $service->cc_location }}</td>
                            <td valign="top" width="30%">{{ $service->cc_lesson }}</td>
                            <td valign="top" width="30%">{{ $service->cc_staff }} </td>
                        </tr>
                    @endforeach
                @endforeach
                </tbody>
            </table>

            @else
                <p>In dem angegebenen Zeitraum wurden keine Termine für die Kinderkirche gefunden.</p>
            @endif

        @endcomponent
    </main>
</div>
</body>
</html>
