<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $months[(int)$month] }} {{ $year }} :: Dienstplan Online</title>

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
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/js/tempusdominus-bootstrap-4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.js"></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Styles -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/tempusdominus-bootstrap-4/5.0.0-alpha14/css/tempusdominus-bootstrap-4.min.css"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/3.5.2/select2.min.css" rel="stylesheet"/>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dienstplan.css') }}" rel="stylesheet">


</head>
<body>
<div id="app">
    <nav class="navbar navbar-expand-md navbar-light navbar-laravel">
        <a class="navbar-brand" href="{{ url('/') }}">
            <span class="fa fa-home"></span> {{ config('app.name', 'Dienstplan Online') }}
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
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th class="no-print">Datum</th>
                        @foreach ($cities as $city)
                            <th>{{ $city->name }}</th>
                        @endforeach
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($days as $day)
                        <tr>
                            <th class="hide-buttons @if($day->date->format('Ymd')==$nextDay->date->format('Ymd')) now @endif
                            @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) limited collapsed @endif
                            @if($day->day_type == \App\Day::DAY_TYPE_LIMITED && (count($day->cities->intersect(Auth::user()->cities))==0)) not-for-city @endif
                                    "
                                @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) title="{{ $day->date->format('d.m.Y') }} (Klicken, um Ansicht umzuschalten)"
                                @endif
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
                                @if (isset($liturgy[$day->id]['perikope']))
                                    <div class="liturgy">
                                        <div class="liturgy-sermon">
                                            <div class="liturgy-color"
                                                 style="background-color: {{ $liturgy[$day->id]['litColor'].';'.($liturgy[$day->id]['litColor'] == 'white' ? 'border-color: darkgray;' : '') }}"
                                                 title="{{ $liturgy[$day->id]['feastCircleName'] }}"></div>
                                            {{ $liturgy[$day->id]['litTextsPerikope'.$liturgy[$day->id]['perikope']] }}
                                        </div>
                                    </div>
                                @endif
                                @can('urlaub-lesen')
                                    @if (count($vacations[$day->id]))
                                        @foreach ($vacations[$day->id] as $vacation)
                                            <div class="vacation">{{ $vacation }}</div>
                                        @endforeach
                                    @endif
                                @endcan
                            </th>
                            @foreach($cities as $city)
                                <td class="@if($day->date->format('Ymd')==$nextDay->date->format('Ymd')) now @endif
                                @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) limited collapsed @endif
                                @if($day->day_type == \App\Day::DAY_TYPE_LIMITED && (count($day->cities->intersect(Auth::user()->cities))==0)) not-for-city @endif
                                @if($day->day_type == \App\Day::DAY_TYPE_LIMITED && ($day->cities->contains($city))) for-city @endif
                                @if(!Auth::user()->cities->contains($city)) not-my-city @endif
                                        "
                                    @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) title="{{ $day->date->format('d.m.Y') }} (Klicken, um Ansicht umzuschalten)"
                                    @endif
                                    data-day="{{ $day->id }}"
                                >
                                    <div class="celldata">
                                        @foreach ($services[$city->id][$day->id] as $service)
                                            <div
                                                    class="service-entry @can('update', $service) editable @endcan
                                                    @if ($service->participants->contains(Auth::user())) mine @endif
                                                    @canany(['gd-kasualien-nur-statistik', 'gd-kasualien-lesen']) @if($service->funerals->count()) funeral @endif @endcanany
                                                            "
                                                    @can('update', $service)
                                                    style="cursor: pointer;"
                                                    title="Klicken, um diesen Eintrag zu bearbeiten"
                                                    onclick="window.location.href='{{ route('services.edit', $service->id) }}';"
                                                    @endcan>
                                                @if (!is_object($service->location))
                                                    <div class="service-time service-special-time">
                                                        {{ strftime('%H:%M', strtotime($service->time)) }} Uhr
                                                    </div>
                                                    <span class="separator">|</span>
                                                    <div class="service-location service-special-location">{{ $service->special_location }}</div>
                                                @else
                                                    <div
                                                            class="service-time @if($service->time !== $service->location->default_time) service-special-time @endif">
                                                        {{ strftime('%H:%M', strtotime($service->time)) }} Uhr
                                                    </div>
                                                    <span class="separator">|</span>
                                                    <div class="service-location">{{ $service->location->name }}</div>
                                                @endif
                                                @if($service->cc) <img src="{{ env('APP_URL') }}img/cc.png"
                                                                       title="Parallel Kinderkirche ({{ $service->cc_location }}) zum Thema {{ '"'.$service->cc_lesson.'"' }}: {{ $service->cc_staff }}"/> @endif
                                                @canany(['gd-kasualien-nur-statistik', 'gd-kasualien-lesen'])
                                                    @if($service->weddings->count())
                                                        <div class="service-description">
                                                            <span class="fa fa-ring"></span> @can('gd-kasualien-lesen') {{ $service->weddingsText() }} @endcan
                                                        </div>
                                                    @endif
                                                    @if($service->funerals->count())
                                                        <div class="service-description">
                                                            <span class="fa fa-cross"></span> @can('gd-kasualien-lesen') {{ $service->funeralsText() }} @endcan
                                                        </div>
                                                    @endif
                                                @endcanany
                                                <div class="float-right service-calendar-button">
                                                    <a class="btn btn-sm btn-secondary"
                                                       href="{{ route('services.ical', $service) }}"
                                                       title="In Outlook übernehmen"><span
                                                                class="fa fa-calendar-alt"></span></a>
                                                </div>
                                                <div class="service-team service-pastor"><span
                                                            class="designation">P: </span>
                                                    @if ($service->need_predicant)
                                                        <span class="need-predicant">Prädikant benötigt</span>
                                                    @else
                                                        @foreach($service->pastors as $participant)
                                                            <span @can('urlaub-lesen') @if (in_array($participant->lastName(), array_keys($vacations[$day->id]))) class="vacation-conflict"
                                                                  title="Konflikt mit Urlaub!" @endif @endcan>{{ $participant->lastName(true) }}</span>
                                                            @if($loop->last) @else | @endif
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <div class="service-team service-organist"><span
                                                            class="designation">O: </span>@foreach($service->organists as $participant){{ $participant->lastName(true) }}@if($loop->last) @else
                                                        | @endif @endforeach</div>
                                                <div class="service-team service-sacristan"><span
                                                            class="designation">M: </span>@foreach($service->sacristans as $participant){{ $participant->lastName(true) }}@if($loop->last) @else
                                                        | @endif @endforeach</div>
                                                <div class="service-description">{{ $service->descriptionText() }}</div>
                                                @canany(['gd-kasualien-lesen', 'gd-kasualien-nur-statistik'])
                                                    @if($service->baptisms->count())
                                                        <div class="service-description">
                                                            @if($service->baptisms->count()) <span class="fa fa-water"
                                                                                                   @can('gd-kasualien-lesen') @if(Auth::user()->cities->contains($city)) title="{{ $service->baptismsText(true) }}" @endif @endcan ></span> {{ $service->baptisms->count() }} @endif
                                                        </div>
                                                    @endif
                                                @endcanany
                                            </div>
                                        @endforeach
                                        @if (Auth::user()->can('create', \App\Service::class) && Auth::user()->writableCities->contains($city))
                                            <a class="btn btn-success btn-sm btn-add-day"
                                               title="Neuen Gottesdiensteintrag hinzufügen"
                                               href="{{ route('services.add', ['date' => $day->id, 'city' => $city->id]) }}"><span
                                                        class="fa fa-plus"></span></a>
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        </tr>


                        @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
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

        $(document).ready(function () {

            // toggle for limited days
            $('.limited').on('click', function (e) {
                if (e.target !== this) return;
                $('[data-day=' + $(this).data('day') + ']').toggleClass('collapsed');
            })

            // toggle all limited days
            $('.btn-toggle-limited-days').on('click', function (e) {
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
