@extends('layouts.app')


@section('title')
    {{ $months[(int)$month] }} {{ $year }}
@endsection

@section('navbar-left')
    <li class="nav-item">
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
                    <span class="fa fa-calendar-day"></span><span class="d-none d-md-inline"> Gehe zu heute</span></a>

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
    </li>
    <li class="nav-item">
        <a class="btn btn-navbar" href="{{ route('ical.link', 'absences') }}">In Outlook abonnieren</a>
    </li>
@endsection

@section('content')
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
                    <th>{{ $user->fullName(false) }}
                        @if($user->id == Auth::user()->id || (Auth::user()->hasPermissionTo('fremden-urlaub-bearbeiten') && (count(Auth::user()->writableCities->intersect($user->homeCities)))))
                            <a class="btn btn-sm btn-success"
                               href="{{ route('absences.create', ['year' => $year, 'month' => $month, 'user' => $user->id]) }}"><span
                                        class="fa fa-plus"></span></a></th>
                    @endif
                    <?php $today = clone($start); $thisHoliday = null; ?>
                    @while($today <= $end)
                        <td class="cal-cell
                                day_{{ $today->format('D') }}
                        @if($today->isToday())today @endif
                        @foreach($holidays as $holiday)
                        @if ($today->between($holiday['start'], $holiday['end'])) holiday <?php $thisHoliday = $holiday; ?> @endif
                        @endforeach
                        @if($services = $user->isBusy($today, true)) busy @endif
                        @if ($absence = $user->isAbsent($today, true)) absent has-absence
    @if($user->id == Auth::user()->id || (Auth::user()->can('editAbsences', $user))) editable @endif
                        @if($absence->getReplacingUserIds()->contains(Auth::user()->id)) replacing @endif
                        @endif
                                "
                            @if(is_object($absence))
                            data-absence="{{ $absence->id }}"
                            @if($user->id == Auth::user()->id || (Auth::user()->can('editAbsences', $user)))
                            data-edit-route="{{ route('absences.edit', ['absence' => $absence, 'startMonth' => $month.'-'.$year]) }}"
                            @endif
                            title="{{ $absence->reason }} ({{ $absence->from->format('d.m.Y') }} @if($absence->to > $absence->from) - {{ $absence->to->format('d.m.Y') }}@endif) V: {{ $absence->replacementText() }}"
                            colspan="{{ $absence->showableDays($start, $end)  }}"
                            <?php $today->addDays($absence->showableDays($start, $end) - 1) ?>
                            @endif
                            @if((!is_object($absence)) && (is_object($thisHoliday)))
                            title="{{ $thisHoliday['name'] }}"
                                @endif
                        >
                            @if(is_object($absence))
                                <span class="absence"><b>{{ $absence->user->lastName() }} {{ $absence->reason }}</b><br/><i>V: {{ $absence->replacementText() }}</i></span>
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
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('td.has-absence').click(function () {
                if ($(this).data('edit-route')) {
                    window.location.href = $(this).data('edit-route');
                }
            });
        });
    </script>
@endsection