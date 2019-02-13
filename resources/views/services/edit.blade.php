@extends('layouts.app')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Gottesdiensteintrag bearbeiten
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form id="frmEdit" method="post" action="{{ route('services.update', $service->id) }}">
                    @method('PATCH')
                    @csrf
                    <div class="form-group">
                        <label for="day_id">Datum</label>
                        <select class="form-control" name="day_id" @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif>
                            @foreach($days as $thisDay)
                                <option value="{{$thisDay->id}}"
                                        @if ($service->day->id == $thisDay->id) selected @endif
                                >{{$thisDay->date->format('d.m.Y')}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="location_id">Kirche</label>
                        <select class="form-control" name="location_id" @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif>
                            @foreach($locations as $thisLocation)
                                <option data-time="{{ strftime('%H:%M', strtotime($thisLocation->default_time)) }}"
                                        value="{{$thisLocation->id}}"
                                        @if (!$service->special_location)
                                            @if ($service->location->id == $thisLocation->id) selected @endif
                                        @endif
                                >
                                    {{$thisLocation->name}}
                                </option>
                            @endforeach
                                <option value=""
                                        @if ($service->special_location) selected @endif
                                >Freie Ortsangabe</option>
                        </select>
                    </div>
                    <div id="special_location" class="form-group">
                        <label for="special_location">Freie Ortsangabe</label>
                        <input id="special_location_input" class="form-control" type="text" name="special_location" value="{{ $service->special_location }}"/>
                        <input type="hidden" name="city_id" value="{{ $service->city_id }}"/>
                    </div>
                    <div class="form-group">
                        <label for="time">Uhrzeit (leer lassen für Standarduhrzeit)</label>
                        <input class="form-control" type="text" name="time" placeholder="HH:MM"
                               value="{{ strftime('%H:%M', strtotime($service->time)) }}" @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="pastor">Pfarrer</label>
                        <input class="form-control" type="text" name="pastor" value="{{ $service->pastor }}" @if ((!(Auth::user()->isAdmin || Auth::user()->canEditField('pastor'))) || ($service->need_predicant)) disabled @endif/>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="need_predicant" value="1"
                               id="needPredicant" @if ($service->need_predicant) checked @endif @if (!(Auth::user()->isAdmin || Auth::user()->canEditField('pastor'))) disabled @endif>
                        <label class="form-check-label" for="needPredicant">
                            Für diesen Gottesdienst wird ein Prädikant benötigt.
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="organist">Organist</label>
                        <input class="form-control" type="text" name="organist" value="{{ $service->organist }}"@if (!(Auth::user()->isAdmin || Auth::user()->canEditField('organist'))) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="sacristan">Mesner</label>
                        <input class="form-control" type="text" name="sacristan" value="{{ $service->sacristan }}" @if (!(Auth::user()->isAdmin || Auth::user()->canEditField('sacristan'))) disabled @endif/>
                    </div>
                    <h4>Besonderheiten</h4>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="baptism" value="1"
                               id="baptism" @if ($service->baptism) checked @endif @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif>
                        <label class="form-check-label" for="baptism">
                            Taufe(n)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="eucharist" value="1"
                               id="eucharist" @if ($service->eucharist) checked @endif @if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif>
                        <label class="form-check-label" for="eucharist">
                            Abendmahl
                        </label>
                    </div>
                    <div class="form-group">
                        <label for="description">Anmerkungen</label>
                        <input type="text" class="form-control" name="description" value="{{ $service->description }}" />
                    </div>
                    <h4>Opfer</h4>
                    <div class="form-group">
                        <label for="offerings_counter1">Opferzähler 1</label>
                        <input class="form-control" type="text" name="offerings_counter1" value="{{ $service->offerings_counter1 }}"@if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="offerings_counter2">Opferzähler 2</label>
                        <input class="form-control" type="text" name="offerings_counter2" value="{{ $service->offerings_counter2 }}"@if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="offering_goal">Opferzweck</label>
                        <input class="form-control" type="text" name="offering_goal" value="{{ $service->offering_goal }}"@if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="offering_description">Anmerkungen zum Opfer</label>
                        <input class="form-control" type="text" name="offering_description" value="{{ $service->offering_description }}"@if (!(Auth::user()->isAdmin || Auth::user()->canEditGeneral)) disabled @endif/>
                    </div>
                    <button id="btnSave" type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
        @if (Auth::user()->canEditGeneral)
        <hr />
        <form action="{{ route('services.destroy', $service->id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" type="submit" title="Gottesdiensteintrag (UNWIDERRUFLICH!) löschen"><span class="fa fa-trash"></span> Gottesdiensteintrag löschen</button>
        </form>
        @endif

    @if (count($service->revisionHistory))
            <hr/>
            <div class="revisions">
                <b>Bisherige Änderungen an diesem Gottesdiensteintrag:</b><br />
                @foreach($service->revisionHistory as $history)
                    @if($history->key == 'created_at' && !$history->old_value)
                        {{ $history->created_at->format('d.m.Y, H:i:s') }}
                        : {{ $history->userResponsible()->name }} hat diesen Eintrag
                        angelegt: {{ $history->newValue() }}<br/>
                    @else
                        {{ $history->created_at->format('d.m.Y, H:i:s') }}
                        : {{ $history->userResponsible()->name }} hat "{{ $history->fieldName() }}" von
                        "{{ $history->oldValue() }}" zu "{{ $history->newValue() }}" geändert.
                        <br/>
                    @endif
                @endforeach
            </div>
        @endif
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

                $('#btnSave').click(function(event){
                    event.preventDefault();
                    $('#frmEdit input, #frmEdit select, #frmEdit textarea').each(function() {
                        $(this).attr('disabled', false);
                    });
                    $('#frmEdit').submit();
                });
            });

        </script>
    @endcomponent
@endsection
