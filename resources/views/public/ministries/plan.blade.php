<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Dienstplan {{ $ministries->implode(', ') }} für {{ $city->name }} :: Pfarrplaner</title>

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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css"/>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pfarrplaner.css') }}" rel="stylesheet">


</head>
<body>
<div id="app">
    <main class="py-1 container">
        @component('components.container')
            <h1 class="mt-2 mb-4">Dienstplan {{ $ministries->implode(', ') }} für {{ $city->name }}</h1>

            <table class="table table-fluid table-striped">
                <thead>
                <tr>
                    <th>Gottesdienst</th>
                    <th>Verantwortlich</th>
                    @foreach($ministries as $ministry)
                    <th>{{ $ministry }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td>
                            @if (($service->titleText() != 'GD') && ($service->titleText()!='Gottesdienst'))
                                <i>{{ $service->titleText() }}</i><br />
                            @endif
                            {{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText() }}<br />
                            {{ $service->locationText() }}
                        </td>
                        <td>
                            P: {{ $service->participantsText('P', true) }}<br />
                            O: {{ $service->participantsText('O', true) }}<br />
                            M: {{ $service->participantsText('M', true) }}<br />
                        </td>
                            @foreach ($ministries as $ministry)
                            <td>
                                {{ $service->participantsText($ministry, true) }}<br />
                            </td>
                            @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endcomponent
    </main>
</div>
</body>
</html>
