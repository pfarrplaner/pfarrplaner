<!-- Left Side Of Navbar -->
@auth
    <div class="button-row no-print btn-toolbar" role="toolbar">
        <div class="btn-group mr-2" role="group">
            <a class="btn btn-default"
               href="{{ route('calendar', ['year' => \Carbon\Carbon::create($year, $month, 1)->subMonth(1)->format('Y-m')]) }}"
               title="Einen Monat zurück">
                <span class="fa fa-backward"></span>
            </a>
            <a class="btn btn-default"
               href="{{ route('calendar', ['year' => \Carbon\Carbon::now()->format('Y-m')]) }}"
               title="Gehe zum aktuellen Monat">
                <span class="fa fa-calendar-day"></span><span class="d-none d-md-inline"> Gehe zu Heute</span></a>

            <div class="btn-group" role="group">
                <button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle"
                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    {{ strftime('%B', $days->first()->date->getTimestamp()) }}
                </button>
                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                    @foreach($months as $thisMonth => $thisMonthText)
                        <a class="dropdown-item"
                           href="{{ route('calendar', ['year' => $year.'-'.$thisMonth]) }}">{{$thisMonthText}}</a>
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
                           href="{{ route('calendar', ['year' => $thisYear.'-'.$month]) }}">{{$thisYear}}</a>
                    @endforeach
                </div>
            </div>
            <a class="btn btn-default"
               href="{{ route('calendar', ['year' => \Carbon\Carbon::create($year, $month, 1)->addMonth(1)->format('Y-m')]) }}"
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
                    <a class="btn btn-success"
                       href="{{ route('days.add', ['year' => $year, 'month' => $month]) }}"
                       title="Angezeigte Tage ändern"><span
                                class="fa fa-calendar-plus"></span><span class="d-none d-md-inline"> Tage</span></a>
                </div>
            @endcan
            <a class="btn btn-default" href="{{route('reports.setup', ['report' => 'ministryRequest'])}}" title="Dienstanfrage per E-Mail senden"><span class="fa fa-envelope"></span> Anfrage senden...</a>
        @else
            Diese Ansicht wird automatisch aktualisiert.
        @endif
        @if(count($filteredLocations))
            <span style="color:red; padding: 7px;" title="Ein Filter ist aktiv! Nur Gottesdienst an folgenden Orten werden angezeigt: {{ \App\Location::whereIn('id', $filteredLocations)->get()->pluck('name')->join(', ') }}">
                <span class="fa fa-filter"></span> Filter aktiv
            </span>
        @endif
    </div>
@endauth
