@extends('layouts.app')

@section('title', 'Im Kalender angezeigte Tage ändern')

@section('content')
    <form method="post" action="{{ route('days.store') }}">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary" id="submit" disabled="disabled">Ansicht ändern</button>
                <a class="btn btn-secondary" href="{{ route('calendar', compact('year', 'month')) }}">Zurück</a>
            @endslot
            <input type="hidden" name="date"/>
            <label>{{ $start->formatLocalized ('%B %Y') }}</label>
            <table width="100%;" id="days">
                <tr>
                    <?php $date = $start->copy() ?>
                    @while($date < $end )
                        <td class="day
                                    @if(isset($existing[$date->format('Y-m-d')]))
                        @if ($existing[$date->format('Y-m-d')]->day_type == \App\Day::DAY_TYPE_LIMITED) day-type-limited @else day-type-default @endif
                            exists
                        @else new @endif
                            " id="day_{{ $date->day }}"
                            data-day="{{ $date->day }}" data-weekday="{{ $date->formatLocalized('%A') }}"
                            data-date="{{ $date->format('d.m.Y') }}"
                            @if(isset($existing[$date->format('Y-m-d')]) && $existing[$date->format('Y-m-d')]->day_type == \App\Day::DAY_TYPE_LIMITED)
                            data-cities="{{ $existing[$date->format('Y-m-d')]->cities->pluck('id')->join(',') }}"
                            @endif
                            @if(isset($existing[$date->format('Y-m-d')]))
                            @if ($existing[$date->format('Y-m-d')]->day_type == \App\Day::DAY_TYPE_LIMITED)
                                title="Dieser Tag existiert bereits, wird aber noch nicht für alle Gemeinden angezeigt."
                            @else
                                title="Dieser Tag existiert bereits."
                            @endif
                            @else
                                title="Diesen Tag neu anlegen"
                            @endif

                        >

                            <span class="weekday-label weekday-label-{{ $date->formatLocalized('%a') }}">{!! $date->formatLocalized('%a') !!}</span><br/>
                            {{ $date->day }}
                        </td>
                        <?php $date->addDay(1) ?>
                    @endwhile
                </tr>
            </table>
            <div class="row">
                <div class="col-md-1 calendar-month">
                    <table class="calendar-month">
                        <tr>
                            <th>
                                <div class="card card-effect">
                                    <div class="card-header day-header-so">
                                        {{ $start->formatLocalized('%A') }}
                                    </div>
                                    <div class="card-body">
                                        <span class="day-label">{{ $start->day }}</span>
                                        <div class="liturgy"></div>
                                    </div>
                                    <div class="card-footer day-name"
                                         title="">{{ $start->formatLocalized('%B') }}</div>
                                </div>
                            </th>
                        </tr>
                    </table>
                </div>
                <div class="col-md-11">
                    <div class="form-group">
                        <label style="display:block;">Anzeige</label>
                        <div class="form-check">
                            <input type="radio" name="day_type" value="{{ \App\Day::DAY_TYPE_DEFAULT }}"
                                   autocomplete="off" id="check-type-default">
                            <label class="form-check-label" style="color: red;">
                                Diesen Tag für alle Gemeinden anzeigen
                            </label>
                            <div class="explain">Bitte nur auswählen, wenn dies ein Sonntag oder allgemeiner kirchlicher
                                Feiertag ist.
                            </div>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="day_type" value="{{ \App\Day::DAY_TYPE_LIMITED }}"
                                   autocomplete="off" id="check-type-limited" checked>
                            <label class="form-check-label" style="background-color: #dcd0ff;">
                                Diesen Tag nur für folgende Gemeinden im Kalender anzeigen:
                            </label>
                            @foreach ($cities as $city)
                                <div class="form-check">
                                    <input
                                        class="form-check-input city-check @if(Auth::user()->cities->contains($city))my-city @else not-my-city @endif"
                                        type="checkbox" name="cities[]" value="{{ $city->id }}"
                                        id="defaultCheck{{$city->id}}">
                                    <label class="form-check-label" for="defaultCheck{{$city->id}}">
                                        {{$city->name}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        function selectDay(n) {
            $('.day').removeClass('selected');
            $('#day_' + n).addClass('selected');
            $('.card .card-body span.day-label').html($('#day_' + n).data('day'));
            $('.card .card-header').html($('#day_' + n).data('weekday'));
            $('input[name=date]').val($('#day_' + n).data('date'));
            $('#check-type-limited').prop('checked', true);
            $('#check-type-default').prop('checked', false);
            $('#submit').removeAttr('disabled');

            $('.city-check').prop('checked', false);
            if ($('#day_' + n).hasClass('day-type-limited')) {
                var cities = $('#day_' + n).data('cities');
                if (cities != '') {
                    String(cities).split(',').forEach(city => {
                        $('#defaultCheck' + city).prop('checked', true);
                    });
                }
            }

        }

        $(document).ready(function () {
            $('.day.new, .day-type-limited').click(function () {
                selectDay($(this).data('day'));
            });


            $('.city-check').change(function () {
                var limit = false;
                $('.city-check').each(function () {
                    limit = limit || $(this).prop('checked');
                });
                if (limit) {
                    $('#check-type-limited').prop('checked', true);
                    $('#check-type-default').prop('checked', false);
                }
            });
        });
    </script>
@endsection

@section('styles')
    <style>
        .card-header {
            padding: 0px 4px !important;
        }

        .day {
            padding: 3px;
            text-align: center;
        }

        .day.new {
            color: gray;
        }

        .day.new:hover, .day.day-type-limited:hover {
            cursor: pointer;
            color: black;
            background-color: #DDFFDD;
        }

        .day.day-type-limited {
            background-color: #DCD0FF;
            font-weight: bold;
        }

        .day.day-type-limited:hover {
            background-color: mediumpurple;
        }

        .day.selected {
            background-color: lightgreen;
            color: black;
        }

        .day.selected.day-type-limited {
            background-color: rebeccapurple;
            color: white;
        }

        .weekday-label-So {
            color: red;
        }

        .day.day-type-default {
            font-weight: bold;
        }

        #days {
            border: solid 1px gray;
            border-radius: 3px;
            margin-bottom: 10px;
        }

        .explain {
            color: black;
            font-size: 0.8em;
            margin-bottom: 20px;
        }

    </style>
@endsection
