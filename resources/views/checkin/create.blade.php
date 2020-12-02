@extends('layouts.app', ['noNav' => 1])

@section('title', 'Einchecken '.($location->at_text ?? $location->name))

@section('content')
    <form id="checkInForm" method="post" action="{{ route('checkin.store', $service) }}">
        @csrf
        @component('components.ui.card')
            @slot('cardHeader') Check-In {{ $location->at_text ?? $location->name }} @endslot
            @slot('cardFooter')
                <input id='submitCheckIn' class="btn btn-primary" type="submit" value="Einchecken" />
            @endslot
            <p>Hier kannst du für den Gottesdienst am {{ $service->dateTime()->formatLocalized('%d.%m.%Y um %H:%M Uhr') }} einchecken.</p>
            <small><b>Bitte beachte: </b>Es handelt sich nicht um eine Sitzplatzreservierung. Ein Check-In garantiert nicht, dass ein Sitzplatz für dich zur Verfügung steht.</small>
            <hr />
            @input(['label' => 'Nachname', 'name' => 'name'])
            @input(['label' => 'Vorname', 'name' => 'first_name'])
            @input(['label' => 'Straße, Hausnummer', 'name' => 'address'])
            @input(['label' => 'PLZ', 'name' => 'zip'])
            @input(['label' => 'Ort', 'name' => 'city'])
            @input(['label' => 'Telefon', 'name' => 'phone'])
            @input(['label' => 'Anzahl Personen', 'name' => 'number'])
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        const checkInFields = ['name', 'first_name', 'address', 'zip', 'city', 'phone', 'number'];

        $(document).ready(function(){

            checkInFields.forEach((item) => {
                var value = localStorage.getItem('pfarrplaner_checkin_'+item);
                if (null != value) $('input[name='+item+']').val(value);
            });

            $('input[name=number]').focus().select();
            checkInFields.reverse().forEach((item) => {
                if ($('input[name='+item+']').val() == '') $('input[name='+item+']').focus();
            });

            $('#submitCheckIn').click(function(event){
                event.preventDefault();

                checkInFields.forEach((item) => {
                    localStorage.setItem('pfarrplaner_checkin_'+item, $('input[name='+item+']').val());
                });

                $('#checkInForm').submit();
            });
        });
    </script>
@endsection
