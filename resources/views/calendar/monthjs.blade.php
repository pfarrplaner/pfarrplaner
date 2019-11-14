<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $months[(int)$month] }} {{ $year }} :: Pfarrplaner</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script
        src="https://code.jquery.com/jquery-3.3.1.min.js"
        integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
        crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment.js"
            integrity="sha256-59IZ5dbLyByZgSsRE3Z0TjDuX7e1AiqW5bZ8Bg50dsU=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/locale/de.js"
            integrity="sha256-wUoStqxFxc33Uz7o+njPIobHc4HJjMQqMXNRDy7X3ps=" crossorigin="anonymous"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/pfarrplaner.css') }}" rel="stylesheet">


</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <a class="navbar-brand" href="{{ url('/') }}">
            <span class="fa fa-home"></span> {{ config('app.name', 'Pfarrplaner') }}
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false"
                aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                @auth
                    <div class="button-row no-print btn-toolbar" role="toolbar">
                        <div class="btn-group mr-2" role="group">
                            <a class="btn btn-default"
                               href="{{ route('calendar', ['year' => $year, 'month' => $month-1]) }}"
                               title="Einen Monat zurück">
                                <span class="fa fa-backward"></span>
                            </a>
                            <a class="btn btn-default"
                               href="{{ route('calendar', ['year' => date('Y'), 'month' => date('m')]) }}"
                               title="Gehe zum aktuellen Monat">
                                <span class="fa fa-calendar-day"></span></a>

                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ strftime('%B', $days->first()->date->getTimestamp()) }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    @foreach($months as $thisMonth => $thisMonthText)
                                        <a class="dropdown-item"
                                           href="{{ route('calendar', ['year' => $year, 'month' => $thisMonth]) }}">{{$thisMonthText}}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop2" type="button" class="btn btn-default dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ strftime('%Y', $days->first()->date->getTimestamp()) }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop2">
                                    @foreach($years as $thisYear)
                                        <a class="dropdown-item"
                                           href="{{ route('calendar', ['year' => $thisYear, 'month' => $month]) }}">{{$thisYear}}</a>
                                    @endforeach
                                </div>
                            </div>
                            <a class="btn btn-default"
                               href="{{ route('calendar', ['year' => $year, 'month' => $month+1]) }}"
                               title="Einen Monat weiter">
                                <span class="fa fa-forward"></span>
                            </a>
                        </div>
                        <div class="btn-group mr-2" role="group">
                            <a class="btn btn-default btn-toggle-limited-days"
                               href="#"
                               title="Alle ausgeblendeten Tage einblenden"><span
                                        class="fa @if(Session::get('showLimitedDays') === true) fa-check-square @else fa-square @endif"></span></a>
                        </div>
                        @can('create', \App\Service::class)
                            <div class="btn-group mr-2" role="group">
                                <a class="btn btn-default"
                                   href="{{ route('days.add', ['year' => $year, 'month' => $month]) }}"
                                   title="Tag hinzufügen"><span
                                        class="fa fa-calendar-plus"></span></a>
                            </div>
                        @endcan
                        <div class="btn-group mr-2" role="group">
                            <a class="btn btn-default"
                               href="{{ route('calendar.printsetup', ['year' => $year, 'month' => $month]) }}"><span
                                    class="fa fa-print"></span></a>
                            <a class="btn btn-default"
                               href="{{ route('reports.list') }}"
                               title="Daten in verschiedenen Formaten ausgeben">
                                Ausgabe...
                            </a>
                        </div>
                    </div>
                @endauth
            </ul>

            @component('components.admin')
            @endcomponent
        </div>
    </nav>

    <main class="py-1">
        <div class="calendar-month">
            @if(session()->get('success'))
                <div class="alert alert-success">
                    {{ session()->get('success') }}
                </div><br/>
            @endif

            <h1 class="print-only">{{ strftime('%B %Y', $days->first()->date->getTimestamp()) }}</h1>
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="no-print">Kirchengemeinde</th>
                    @foreach ($days as $day)
                        <th class="hide-buttons @if($day->date->format('Ymd')==$nextDay->date->format('Ymd')) now @endif
                                @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) limited collapsed @endif
                                @if($day->day_type == \App\Day::DAY_TYPE_LIMITED && (count($day->cities->intersect(Auth::user()->cities))==0)) not-for-city @endif
                                " @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) title="{{ $day->date->format('d.m.Y') }} (Klicken, um Ansicht umzuschalten)" @endif
                            data-day="{{ $day->id }}"
                        >
                            @if ($day->date->dayOfWeek > 0) <span class="special-weekday">{{ strftime('%a', $day->date->getTimestamp()) }}
                                .</span>, @endif
                            {{ $day->date->format('d.m.Y') }}
                            @can('gd-allgemein-bearbeiten')
                            <a class="btn btn-default btn-sm" role="button"
                               href="{{ route('days.edit', $day->id) }}"><span class="fa fa-edit"></span></a>
                            @endcan
                            @can('tag-loeschen')
                                <form action="{{ route('days.destroy', $day->id)}}" method="post"
                                      style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm" type="submit"><span
                                            class="fa fa-trash"></span>
                                    </button>
                                </form>
                            @endcan
                            @if ($day->name)
                                <div class="day-name">{{ $day->name }}</div> @endif
                            @if ($day->description)
                            <div class="day-description">{{ $day->description }}</div> @endif
                            <div class="liturgy">
                                <div class="waiting2" data-route="{{ route('liturgicalInfo', ['dayId' => $day->id]) }}">
                                    <span class="fa fa-spin fa-spinner"></span>
                                </div>
                            </div>
                            @can('urlaub-lesen')
                                <div class="waiting2" data-route="{{ route('vacationsByDay', ['dayId' => $day->id]) }}">
                                    <span class="fa fa-spin fa-spinner"></span>
                                </div>
                            @endcan
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($cities as $city)
                    <tr class="print-only">
                        <td colspan="{{ count($days) }}"><h2>{{ $city->name }}</h2></td>
                    </tr>
                    <tr>
                        <td class="no-print">{{$city->name}}</td>
                        @foreach ($days as $day)
                            <td class="@if($day->date->format('Ymd')==$nextDay->date->format('Ymd')) now @endif
                            @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) limited collapsed @endif
                            @if($day->day_type == \App\Day::DAY_TYPE_LIMITED && (count($day->cities->intersect(Auth::user()->cities))==0)) not-for-city @endif
                            @if($day->day_type == \App\Day::DAY_TYPE_LIMITED && ($day->cities->contains($city))) for-city @endif
                            @if(!Auth::user()->cities->contains($city)) not-my-city @endif
                                    " @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) title="{{ $day->date->format('d.m.Y') }} (Klicken, um Ansicht umzuschalten)" @endif
                            data-day="{{ $day->id }}"
                            >
                                <div class="celldata">
                                    <div class="waiting" data-route="{{ route('servicesByCityAndDay', ['city' => $city->id, 'day' => $day->id]) }}">
                                        <span class="fa fa-spin fa-spinner"></span>
                                    </div>
                                @if (Auth::user()->can('create', \App\Service::class) && Auth::user()->cities->contains($city))
                                    <a class="btn btn-success btn-sm btn-add-day" title="Neuen Gottesdiensteintrag hinzufügen"
                                       href="{{ route('services.add', ['date' => $day->id, 'city' => $city->id]) }}"><span
                                            class="fa fa-plus"></span></a>
                                @endif
                                </div>
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
            <hr/>
        </div>
    </main>
    <script>
        function setLimitedColumnStatus() {
            if ($('.btn-toggle-limited-days').find('span').first().hasClass('fa-square')) {
                $('.btn-toggle-limited-days').attr('title', 'Alle ausgeblendeten Tage einblenden');
                $('.limited').addClass('collapsed');
                $.get('{{ route('showLimitedColumns', ['switch' => 0]) }}');
            } else {
                $('.btn-toggle-limited-days').attr('title', 'Alle ausblendbaren Tage ausblenden');
                $('.limited').removeClass('collapsed');
                $.get('{{ route('showLimitedColumns', ['switch' => 1]) }}');
            }
        }

        $(document).ready(function(){

            $('.waiting').each(function(){
                axios.get($(this).data('route'), {
                }).then((response) => {
                    $(this).replaceWith(response.data);
                    // open limited days with services that belong to me
                    $('.limited .service-entry.mine').parent().parent().removeClass('collapsed');
                }).catch((error)=>{
                    console.log(error.response.data)
                });
            });

            $('.waiting2').each(function(){
                axios.get($(this).data('route'), {
                }).then((response) => {
                    $(this).replaceWith(response.data);
                }).catch((error)=>{
                    console.log(error.response.data)
                });
            });

            // toggle for limited days
            $('.limited').on('click', function(e) {
                if (e.target !== this) return;
                $('[data-day='+$(this).data('day')+']').toggleClass('collapsed');
            })

            // toggle all limited days
            $('.btn-toggle-limited-days').on('click', function(e){
                e.preventDefault();
                $(this).find('span').toggleClass('fa-square').toggleClass('fa-check-square');
                setLimitedColumnStatus();
            });
            setLimitedColumnStatus();

            // open limited days with services that belong to me
            $('.limited .service-entry.mine').parent().parent().removeClass('collapsed');
        });

    </script>
</div>
</body>
</html>
