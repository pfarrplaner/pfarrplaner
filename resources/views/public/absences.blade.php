<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Vertretungen :: Dienstplan Online</title>

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

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Styles -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css"/>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dienstplan.css') }}" rel="stylesheet">


</head>
<body>
<div id="app">
    <main class="py-1">
        @component('components.container')
            <h1>Pfarrervertretungen bis einschl. Ende {{ strftime('%B', $end->getTimestamp()) }}</h1>
            <p><b>Für die Kirchengemeinden:</b> {{ $cities->implode('name', ', ') }}</p>

            <table class="table table-fluid table-striped">
                <thead>
                <tr>
                    <th>KG</th>
                    <th>Abwesend</th>
                    <th>Vertretung</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($vacations as $vacation)
                    <tr>
                        <td>{{ $vacation['away']->cities->implode('name', ' / ') }}</td>
                        <td>{{ strftime('%a., %d.%m.%Y', $vacation['start']->getTimestamp()) }} - {{ strftime('%a., %d.%m.%Y', $vacation['end']->getTimestamp()) }}<br />
                            <b>Pfarrer {{ $vacation['away']->name }}</b><br />{{ $vacation['away']->office }}</td>
                        <td>
                            @foreach($vacation['substitute'] as $sub)
                                <b>Pfarrer {{ $sub->name }}</b><br />
                                {{ $sub->office }}<br />
                                Tel. {{ $sub->phone }}<br />
                                E-Mail <a href="mailto:{{$sub->email}}">{{ $sub->email }}</a> @if (!$loop->last) <br /><br />@endif
                            @endforeach
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endcomponent
    </main>
</div>
</body>
</html>
