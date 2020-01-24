@extends('layouts.app')

@section('title', 'Bestattung hinzufügen : Schritt 2')

@section('content')
    <form method="post" action="{{ route('funerals.wizard.step3') }}" enctype="multipart/form-data">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary" id="submit">Weiter &gt;</button>
            @endslot

            @hidden(['name' => 'city', 'value'=> $city->id])
            @hidden(['name' => 'day', 'value'=> $day->id])
            @locationselect(['name' => 'location_id', 'label' => 'Kirche', 'locations' => $locations])
            @input(['name' => 'special_location', 'label' => 'Freie Ortsangabe', 'id' => 'special_location'])
            @hidden(['name' => 'city_id', 'value' => $city->id])
            @input(['name' => 'time', 'label' => 'Uhrzeit (leer lassen für Standarduhrzeit)', 'placeholder' => 'HH:MM'])
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        function setDefaultTime() {
            if ($('select[name=location_id]').val() == '0') {
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
