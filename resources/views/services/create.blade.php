@extends('layouts.app')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Gottesdienst hinzufügen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('services.store') }}">
                    <div class="form-group">
                        @csrf
                        <label for="day_id">Datum</label>
                        <select class="form-control" name="day_id">
                            @foreach($days as $thisDay)
                                <option value="{{$thisDay->id}}"
                                        @if (($day) && ($day->id == $thisDay->id)) selected @endif
                                >{{$thisDay->date->format('d.m.Y')}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="location_id">Kirche</label>
                        <select class="form-control" name="location_id">
                            @foreach($locations as $thisLocation)
                                <option data-time="{{ strftime('%H:%M', strtotime($thisLocation->default_time)) }}"
                                        value="{{$thisLocation->id}}">{{$thisLocation->name}}</option>
                            @endforeach
                            <option value="">Freie Ortsangabe</option>
                        </select>
                    </div>
                    <div id="special_location" class="form-group">
                        <label for="special_location">Freie Ortsangabe</label>
                        <input id="special_location_input" class="form-control" type="text" name="special_location"/>
                        <input type="hidden" name="city_id" value="{{ $city->id }}"/>
                    </div>
                    <div class="form-group">
                        <label for="time">Uhrzeit (leer lassen für Standarduhrzeit)</label>
                        <input class="form-control" type="text" name="time" placeholder="HH:MM"/>
                    </div>
                    <div class="form-group">
                        <label for="pastor">Pfarrer*in</label>
                        <input class="form-control" type="text" name="pastor"/>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="need_predicant" value="1"
                               id="needPredicant">
                        <label class="form-check-label" for="needPredicant">
                            Für diesen Gottesdienst wird ein Prädikant benötigt.
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="organist">Organist*in</label>
                        <input class="form-control" type="text" name="organist"/>
                    </div>
                    <div class="form-group">
                        <label for="sacristan">Mesner*in</label>
                        <input class="form-control" type="text" name="sacristan"/>
                    </div>
                    <h4>Besonderheiten</h4>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="baptism" value="1"
                               id="baptism" @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif>
                        <label class="form-check-label" for="baptism">
                            Taufe(n)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="eucharist" value="1"
                               id="eucharist" @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif>
                        <label class="form-check-label" for="eucharist">
                            Abendmahl
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="description">Anmerkungen</label>
                        <input type="text" class="form-control" name="description"  />
                    </div>
                    <h4>Opfer</h4>
                    <div class="form-group">
                        <label for="offerings_counter1">Opferzähler*in 1</label>
                        <input class="form-control" type="text" name="offerings_counter1" @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="offerings_counter2">Opferzähler*in 2</label>
                        <input class="form-control" type="text" name="offerings_counter2" @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="offering_goal">Opferzweck</label>
                        <input class="form-control" type="text" name="offering_goal" @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="offering_description">Anmerkungen zum Opfer</label>
                        <input class="form-control" type="text" name="offering_description" @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif/>
                    </div>
                    <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </form>
            </div>
        </div>
        </div>
        <script>
            function setDefaultTime() {
                if ($('select[name=location_id]').val() == '') {
                    $('input[name=time]').attr('placeholder', 'HH:MM');
                    $('#special_location').show();
                    $('#special_location input').first().focus();
                } else {
                    $('input[name=time]').attr('placeholder', 'HH:MM, leer lassen für: ' + ($('select[name=location_id]').children("option:selected").data('time')));
                    $('#special_location_input').val('');
                    $('#special_location').hide();
                }
            }

            $(document).ready(function () {
                setDefaultTime();

                if ($('select[name=location_id] option').length > 2) {
                    $('select[name=location_id]').focus()
                } else {
                    $('input[name=pastor]').focus();
                }

                $('select[name=location_id]').change(function () {
                    setDefaultTime();
                });

                $('#needPredicant').change(function(){
                    if ($(this).prop( "checked" )) {
                        $('input[name=pastor]').val('').attr('disabled', true);
                    } else {
                        $('input[name=pastor]').attr('disabled', false).focus();
                    }
                });

            });

        </script>
    @endcomponent
@endsection
