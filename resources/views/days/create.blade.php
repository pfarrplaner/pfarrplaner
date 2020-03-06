@extends('layouts.app')

@section('title', 'Tag zum Kalender hinzufügen')

@section('content')
    <form method="post" action="{{ route('days.store') }}">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary" id="submit">Hinzufügen</button>
            @endslot
            <input type="hidden" name="date"/>
            <table width="100%;" id="days">
                <tr>
                    <?php $date = \Carbon\Carbon::create($year, $month, 1, 0, 0, 0); ?>
                    <?php $end = $date->copy()->addMonth(1)->subSecond(1); ?>
                    @while($date < $end )
                        <td class="day
                                    @if($existing->contains($date)) exists @else new @endif
                            " id="day_{{ $date->day }}"
                            data-day="{{ $date->day }}" data-weekday="{{ $date->formatLocalized('%A') }}"
                            data-date="{{ $date->format('d.m.Y') }}">
                            {!! $date->formatLocalized('%a') !!}<br/>
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
                                        Sonntag
                                    </div>
                                    <div class="card-body">
                                        <span>1</span>
                                        <div class="liturgy"></div>
                                    </div>
                                    <div class="card-footer day-name"
                                         title=""></div>
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
                            <div class="explain">Bitte nur auswählen, wenn dies ein Sonntag oder allgemeiner kirchlicher Feiertag ist.</div>
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
            $('.card .card-body span').html($('#day_' + n).data('day'));
            $('.card .card-header').html($('#day_' + n).data('weekday'));
            $('input[name=date]').val($('#day_' + n).data('date'));
        }

        $(document).ready(function () {
            selectDay({{ $days->first() }});

            $('.day.new').click(function () {
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
                } else {
                    $('#check-type-limited').prop('checked', false);
                    $('#check-type-default').prop('checked', true);
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

        .day.new:hover {
            border: solid 1px gray;
            border-radius: 3px;
            cursor: pointer;
        }

        .day.selected {
            background-color: yellow;
        }

        .day.exists {
            color: lightgray;
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
