<!-- Left Side Of Navbar -->
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
                <span class="fa fa-calendar-day"></span><span class="d-none d-md-inline"> Gehe zu Heute</span></a>

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
                    <a class="btn btn-success"
                       href="{{ route('days.add', ['year' => $year, 'month' => $month]) }}"
                       title="Tag hinzufügen"><span
                                class="fa fa-calendar-plus"></span><span class="d-none d-md-inline"> Tag hinzufügen</span></a>
                </div>
            @endcan
        @else
            Diese Ansicht wird automatisch aktualisiert.
        @endif
    </div>
@endauth
