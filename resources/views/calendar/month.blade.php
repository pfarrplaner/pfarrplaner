@extends('layouts.app', ['noNavBar' => $slave])


@section('title')
    {{ $months[(int)$month] }} {{ $year }}
@endsection

@section('navbar')
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
                @if (!$slave)
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
                @else
                    Diese Ansicht wird automatisch aktualisiert.
                @endif
            </div>
        @endauth
    </ul>
@endsection

@section('content')
    <div class="loader">
        <h1>Die Kalenderansicht wird geladen... <span class="fa fa-spin fa-spinner"></span></h1>
    </div>
    <div class="page" style="display: none;">
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
                    <th class="no-print">
                        @if ($slave)
                            <div class="badge badge-info">
                                <span id="heartbeat" class="fa fa-sync" ></span>
                                 Auto-Update
                                <br />
                            </div><br />
                        @endif
                        Kirchengemeinde</th>
                    @foreach ($days as $day)
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
                                <div class="day-name"
                                     title="@if(isset($liturgy[$day->id]['litProfileGist'])){{ $liturgy[$day->id]['litProfileGist'] }}@endif">{{ $day->name }}</div>
                            @else
                                <div class="day-name"
                                     title="@if(isset($liturgy[$day->id]['litProfileGist'])){{ $liturgy[$day->id]['litProfileGist'] }}@endif">@if(isset($liturgy[$day->id]['title'])){{ $liturgy[$day->id]['title'] }}@endif</div>
                            @endif
                            @if ($day->description)
                                <div class="day-description">{{ $day->description }}</div> @endif
                            @if (isset($liturgy[$day->id]['perikope']))
                                <div class="liturgy">
                                    <div class="liturgy-sermon">
                                        <div class="liturgy-color"
                                             style="background-color: {{ $liturgy[$day->id]['litColor'].';'.($liturgy[$day->id]['litColor'] == 'white' ? 'border-color: darkgray;' : '') }}"
                                             title="{{ $liturgy[$day->id]['feastCircleName'] }}"></div>
                                        <a href="{{ $liturgy[$day->id]['litTextsPerikope'.$liturgy[$day->id]['perikope'].'Link'] }}"
                                           target="_blank" title="Klicken, um den Text in einem neuen Tab zu lesen">
                                            {{ $liturgy[$day->id]['litTextsPerikope'.$liturgy[$day->id]['perikope']] }}</a>
                                    </div>
                                </div>
                            @endif
                            @can('urlaub-lesen')
                                @if (isset($vacations[$day->id]) && count($vacations[$day->id]))
                                    @foreach ($vacations[$day->id] as $vacation)
                                        <div class="vacation"
                                             title="{{ $vacation->user->fullName(true) }}: {{ $vacation->reason }} ({{ $vacation->durationText() }}) [{{ $vacation->replacementText('V:') }}]">
                                            <span class="fa fa-globe-europe"></span> {{ $vacation->user->lastName() }}
                                        </div>
                                    @endforeach
                                @endif
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
                                    "
                                @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) title="{{ $day->date->format('d.m.Y') }} (Klicken, um Ansicht umzuschalten)"
                                @endif
                                data-day="{{ $day->id }}"
                            >
                                <div class="celldata">
                                    @foreach ($services[$city->id][$day->id] as $service)
                                        <div
                                                class="service-entry @can('update', $service) editable @endcan
                                                @if ((!$slave) && $service->participants->contains(Auth::user())) mine @endif
                                                @if (($slave) && ($highlight == $service->id)) highlighted @endif
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
                                                @foreach($service->ministries() as $ministry => $people)
                                                    <div class="service-team"><span class="designation">{{ $ministry }}: </span>{{ $people->implode('planName', ' | ') }}</div>
                                                @endforeach
                                            <div class="service-description">{{ $service->descriptionText() }}</div>
                                            @if($service->internal_remarks)<div class="service-description" title="{{ $service->internal_remarks }}"><span class="fa fa-eye-slash" title="Anmerkung nur für den internen Gebrauch"></span> {{ \App\Tools\StringTool::trimToLen($service->internal_remarks, 150) }}</div>@endif
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
                                    @if ((!$slave) && Auth::user()->can('create', \App\Service::class) && Auth::user()->writableCities->contains($city))
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
                </tbody>
            </table>
            <hr/>
        </div>
    </div>
    @if($slave)
        <script>var slave = 1;</script>
    @else
        <script>var slave = 0;</script>
    @endif
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

        var lastUpdate = null;

        function checkForUpdates() {
            $('#heartbeat').removeClass('fa-sync').addClass('fa-heart').css('color','red');
            fetch('{{ route('lastUpdate') }}')
                .then(response => response.json())
                .then(data => {
                    if (null === lastUpdate) {
                        lastUpdate = data.update
                    } else {
                        if (data.update != lastUpdate) {
                            $(".page").delay(800).fadeOut(400, function () {
                                $(".loader").fadeIn(400);
                            });
                            window.location.href = data.route;
                        }
                    }
                    $('#heartbeat').removeClass('fa-heart').addClass('fa-sync').css('color','#fff');
                })
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

        $(".loader").delay(800).fadeOut(400, function () {
            $(".page").fadeIn(400);
            if (slave) {
                var t = setInterval(checkForUpdates, 2000);
            }
        });


    </script>
@endsection
