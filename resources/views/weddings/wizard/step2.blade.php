@extends('layouts.app')

@section('title', 'Trauung hinzufügen : Schritt 2')

@section('content')
    <form method="post" action="{{ route('weddings.wizard.step3') }}" enctype="multipart/form-data">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary" id="submit">Weiter &gt;</button>
            @endslot
            <input type="hidden" name="city" value="{{ $city->id }}"/>
            <input type="hidden" name="day" value="{{ $day->id }}"/>
            <div class="form-group">
                <label for="location_id">Kirche</label>
                <select class="form-control" name="location_id" @cannot('gd-allgemein-bearbeiten') disabled @endcannot>
                    @foreach($locations as $thisLocation)
                        <option data-time="{{ strftime('%H:%M', strtotime($thisLocation->default_time)) }}"
                                data-cc="{{ $thisLocation->cc_default_location }}"
                                value="{{$thisLocation->id}}">{{$thisLocation->name}}</option>
                    @endforeach
                    <option value="">Freie Ortsangabe</option>
                </select>
            </div>
            <div id="special_location" class="form-group">
                <label for="special_location">Freie Ortsangabe</label>
                <input id="special_location_input" class="form-control" type="text" name="special_location"
                       @cannot('gd-allgemein-bearbeiten') disabled @endcannot/>
                <input type="hidden" name="city_id" value="{{ $city->id }}"/>
            </div>
            <div class="form-group">
                <label for="time">Uhrzeit (leer lassen für Standarduhrzeit)</label>
                <input class="form-control" type="text" name="time" placeholder="HH:MM"
                       @cannot('gd-allgemein-bearbeiten') disabled @endcannot />
            </div>
        @endcomponent
    </form>
@endsection

@section('scripts')
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
                $('input[name=time]').focus();
            }

            $('select[name=location_id]').change(function () {
                setDefaultTime();
            });

        });

    </script>
@endsection