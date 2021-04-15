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
            @locationselect(['name' => 'location_id', 'label' => 'Kirche', 'locations' => $locations, 'grouped' => 'true'])
            @hidden(['name' => 'city_id', 'value' => $city->id])
            @input(['name' => 'time', 'label' => 'Uhrzeit (leer lassen für Standarduhrzeit)', 'placeholder' => 'HH:MM'])
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        var defaultTime = {};
        @foreach($locations as $theseLocations)
        @foreach($theseLocations as $location)defaultTime['{{ $location->id }}'] = '{{ substr($location->default_time,0, 5) }}';@endforeach
        @endforeach

        function setDefaultTime() {
            var loc = $('select[name=location_id] option:selected').first().val();
            if (undefined != defaultTime[loc]) {
                $('input[name=time]').attr('placeholder', 'HH:MM, leer lassen für: ' + defaultTime[loc]);
            } else {
                $('input[name=time]').attr('placeholder', '');
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
