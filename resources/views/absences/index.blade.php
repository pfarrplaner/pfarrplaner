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

    <!-- Fonts -->
    <link rel="dns-prefetch" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css"
          integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
    <!-- Styles -->
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
                               href="{{ route('absences.index', ['year' => $year, 'month' => $month-1]) }}"
                               title="Einen Monat zurück">
                                <span class="fa fa-backward"></span>
                            </a>
                            <a class="btn btn-default"
                               href="{{ route('absences.index', ['year' => date('Y'), 'month' => date('m')]) }}"
                               title="Gehe zum aktuellen Monat">
                                <span class="fa fa-calendar-day"></span></a>

                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" class="btn btn-default dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ strftime('%B', $now->getTimestamp()) }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                    @foreach($months as $thisMonth => $thisMonthText)
                                        <a class="dropdown-item"
                                           href="{{ route('absences.index', ['year' => $year, 'month' => $thisMonth]) }}">{{$thisMonthText}}</a>
                                    @endforeach
                                </div>
                            </div>
                            <div class="btn-group" role="group">
                                <button id="btnGroupDrop2" type="button" class="btn btn-default dropdown-toggle"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    {{ strftime('%Y', $now->getTimestamp()) }}
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop2">
                                    @foreach($years as $thisYear)
                                        <a class="dropdown-item"
                                           href="{{ route('absences.index', ['year' => $thisYear, 'month' => $month]) }}">{{$thisYear}}</a>
                                    @endforeach
                                </div>
                            </div>
                            <a class="btn btn-default"
                               href="{{ route('absences.index', ['year' => $year, 'month' => $month+1]) }}"
                               title="Einen Monat weiter">
                                <span class="fa fa-forward"></span>
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
        <div class="table-responsive tbl-absences">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th rowspan="2">Name</th>
                    @for($m=1; $m<=3; $m++)
                        <th colspan="{{ (clone $start)->addMonth($m)->subDay(1)->format('d') }}">{{ $months[(clone $start)->addMonth($m-1)->format('n')] }}</th>
                    @endfor
                </tr>
                <tr>
                    @for($m=1; $m<=3; $m++)
                        @for($d=1;$d<=(clone $start)->addMonth($m)->subDay(1)->format('d'); $d++)
                            <th class="cal-cell
                            {{ $today = (clone $start)->addMonth($m-1)->addDay($d-1) }}

                            @foreach($holidays as $holiday)
                            @if ($today->between($holiday['start'], $holiday['end'])) holiday @endif
                            @endforeach

                            day_{{ $today->format('D') }}

                            @if($today->isToday())today @endif
">{{ sprintf('%02d', $d)}}</th>
                        @endfor
                    @endfor
                </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                        <tr>
                            <th>{{ $user->fullName(false) }}
                                <a class="btn btn-sm btn-success" href="{{ route('absences.create', ['year' => $year, 'month' => $month, 'user' => $user->id]) }}"><span class="fa fa-plus"></span></a></th>
                            <?php $today = clone($start); $thisHoliday = null; ?>
                            @while($today <= $end)
                                <td class="cal-cell
                                day_{{ $today->format('D') }}
                                @if($today->isToday())today @endif
@foreach($holidays as $holiday)
@if ($today->between($holiday['start'], $holiday['end'])) holiday <?php $thisHoliday = $holiday; ?> @endif
@endforeach
@if($services = $user->isBusy($today, true)) busy @endif
@if ($absence = $user->isAbsent($today, true)) absent has-absence @endif
@if ($replacement = $user->isReplacement($today, true)) replacement has-absence @endif
                                        "
                                @if(is_object($absence))
                                    data-absence="{{ $absence->id }}"
                                    data-edit-route="{{ route('absences.edit', ['absence' => $absence, 'startMonth' => $month.'-'.$year]) }}"
                                    title = "{{ $absence->reason }}  @if($absence->replacement()->first()) V: {{ $absence->replacement()->first()->fullName() }} @endif"
                                    colspan="{{ $absence->to->diffInDays($absence->from)+1  }}" <?php $today->addDays($absence->to->diffInDays($absence->from)) ?>
                                @endif
                                @if(is_object($replacement))
                                    data-absence="{{ $replacement->id }}"
                                    data-edit-route="{{ route('absences.edit', ['absence' => $replacement, 'startMonth' => $month.'-'.$year]) }}"
                                    title = "Vertretung für {{ $replacement->user()->first()->fullName() }}"
                                    colspan="{{ $replacement->to->diffInDays($replacement->from)+1  }}" <?php $today->addDays($replacement->to->diffInDays($replacement->from)) ?>
                                @endif
                                @if((!is_object($absence)) && (!is_object($replacement)) && (is_object($thisHoliday)))
                                    title = "{{ $thisHoliday['name'] }}"
                                @endif
                                >
                                    @if(is_object($absence))
                                        <span class="absence">{{ $absence->reason }}<br />@if($absence->replacement()->first()) V: {{ $absence->replacement()->first()->fullName() }} @endif</span>
                                        @elseif (is_object($replacement)) V: {{ $replacement->user()->first()->last_name }}
                                        @else &nbsp;
                                        @endif
                                </td>
                                <?php $today->addDay(1) ?>
                            @endwhile
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </main>
    <script>
        $(document).ready(function(){
            $('td.has-absence').click(function(){
                window.location.href=$(this).data('edit-route');
            });
        })
    </script>

</div>
</body>
</html>