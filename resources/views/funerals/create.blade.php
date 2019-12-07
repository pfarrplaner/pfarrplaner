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
                    <input type="hidden" name="wizard" value="{{ $wizard }}"/>
                    <input type="hidden" name="service" value="{{ $service->id }}"/>
                    <div class="form-group">
                        <label for="buried_name">Name</label>
                        <input type="text" class="form-control" name="buried_name" placeholder="Nachname, Vorname"/>
                    </div>
                    <div class="form-group">
                        <label for="dob">Geburtsdatum</label>
                        <input type="text" class="form-control datepicker" name="dob"/>
                    </div>
                    <div class="form-group">
                        <label for="dod">Sterbedatum</label>
                        <input type="text" class="form-control datepicker" name="dod"/>
                    </div>
                    <div class="form-group">
                        <label for="buried_address">Adresse</label>
                        <input type="text" class="form-control" name="buried_address"/>
                    </div>
                    <div class="form-group">
                        <label for="buried_zip">PLZ</label>
                        <input type="text" class="form-control" name="buried_zip"/>
                    </div>
                    <div class="form-group">
                        <label for="buried_city">Ort</label>
                        <input type="text" class="form-control" name="buried_city"/>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="text">Text</label>
                        <input type="text" class="form-control" name="text"/>
                    </div>
                    <div class="form-group">
                        <label for="announcement">Abkündigen am</label>
                        <input type="text" class="form-control datepicker" name="announcement" placeholder="tt.mm.jjjj"/>
                    </div>
                    <div class="form-group">
                        <label for="type">Bestattungsart</label>
                        <select id="selType" class="form-control" name="type">
                            <option>Erdbestattung</option>
                            <option>Trauerfeier</option>
                            <option>Trauerfeier mit Urnenbeisetzung</option>
                            <option>Urnenbeisetzung</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="wake">Datum der vorhergehenden Trauerfeier</label>
                        <input id="dateWake" type="text" class="form-control datepicker" name="wake" placeholder="tt.mm.jjjj"
                               disabled/>
                    </div>
                    <div class="form-group">
                        <label for="wake_location">Ort der vorhergehenden Trauerfeier</label>
                        <input id="locWake" type="text" class="form-control" name="wake_location" disabled/>
                    </div>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Angehörige(r) @endslot
                    <div class="form-group">
                        <label for="relative_name">Name</label>
                        <input type="text" class="form-control" name="relative_name" placeholder="Nachname, Vorname"/>
                    </div>
                    <div class="form-group">
                        <label for="relative_address">Adresse</label>
                        <input type="text" class="form-control" name="relative_address"/>
                    </div>
                    <div class="form-group">
                        <label for="relative_zip">PLZ</label>
                        <input type="text" class="form-control" name="relative_zip"/>
                    </div>
                    <div class="form-group">
                        <label for="relative_city">Ort</label>
                        <input type="text" class="form-control" name="relative_city"/>
                    </div>
                    <div class="form-group">
                        <label for="relative_contact_data">Kontakt</label>
                        <textarea class="form-control" name="relative_contact_data"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="appointment">Trauergespräch</label>
                        <input type="text" class="form-control datetimepicker" name="appointment"/>
                    </div>
                @endcomponent
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        function setFieldStates() {
            $('#dateWake').prop('disabled', ($('#selType').val() == 'Urnenbeisetzung' ? false : 'disabled'));
            $('#locWake').prop('disabled', ($('#selType').val() == 'Urnenbeisetzung' ? false : 'disabled'));
        }

        $(document).ready(function () {
            $('#selType').change(function () {
                setFieldStates()
            });
            setFieldStates();
        });
    </script>
@endsection