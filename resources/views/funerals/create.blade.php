@extends('layouts.app')

@section('title')Bestattung am {{ $service->day->date->format('d.m.Y') }} hinzufügen @endsection

@section('content')
    <form method="post" action="{{ route('funerals.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Informationen zur Bestattung @endslot
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary" id="submit">Hinzufügen</button>
                    @endslot
                        @hidden(['name' => 'wizard', 'value' => $wizard])
                        @hidden(['name' => 'service_id', 'value' => $service->id])
                        @input(['name' => 'buried_name', 'label'=> 'Name', 'placeholder' => 'Nachname, Vorname'])
                        @input(['name' => 'dob', 'label'=> 'Geburtsdatum', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker'])
                        @input(['name' => 'dod', 'label'=> 'Sterbedatum', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker'])
                        @input(['name' => 'buried_address', 'label'=> 'Adresse'])
                        @input(['name' => 'buried_zip', 'label'=> 'PLZ'])
                        @input(['name' => 'buried_city', 'label'=> 'Ort'])
                    <hr/>
                        @input(['name' => 'text', 'label'=> 'Text'])
                        @input(['name' => 'announcement', 'label'=> 'Abkündigen am', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker'])
                        @select(['name' => 'type', 'label' => 'Bestattungsart', 'items' => ['Erdbestattung', 'Trauerfeier', 'Trauerfeier mit Urnenbeisetzung', 'Urnenbeisetzung'], 'id' => 'selType'])
                        @input(['name' => 'wake', 'label'=> 'Datum der vorhergehenden Trauerfeier', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker', 'id' => 'dateWake'])
                        @input(['name' => 'wake_location', 'label'=> 'Ort der vorhergehenden Trauerfeier', 'id' => 'locWake'])
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Angehörige(r) @endslot
                    @input(['name' => 'relative_name', 'label'=> 'Name', 'placeholder' => 'Nachname, Vorname'])
                    @input(['name' => 'relative_address', 'label'=> 'Adresse'])
                    @input(['name' => 'relative_zip', 'label'=> 'PLZ'])
                    @input(['name' => 'relative_city', 'label'=> 'Ort'])
                    @textarea(['name' => 'relative_contact_data', 'label' => 'Kontakt'])
                    @datetimepicker(['name' => 'appointment', 'label' => 'Trauergespräch', 'placeholder' => 'TT.MM.JJJJ hh:mm'])
                @endcomponent
                @include('components.attachments')
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>var attachments = 0;</script>
    <script src="{{ asset('js/pfarrplaner/attachments.js') }}"></script>
    <script>
        function setFieldStates() {
            if ($('#selType_input option:selected').val() == 'Urnenbeisetzung') {
                $('#dateWake_input').prop('disabled', false);
                $('#locWake_input').prop('disabled', false);
            } else {
                $('#dateWake_input').prop('disabled', 'disabled');
                $('#locWake_input').prop('disabled', 'disabled');
            }
        }

        $(document).ready(function () {
            $('#selType_input').change(function () {
                setFieldStates()
            });
            setFieldStates();
        });
    </script>
@endsection
