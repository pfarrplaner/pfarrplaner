@extends('layouts.app')

@section('title', 'Bestattung bearbeiten')

@section('content')
    <form method="post" action="{{ route('funerals.update', $funeral->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row">
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Informationen zur Bestattung @endslot
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary" id="submit">Speichern</button>
                    @endslot
                    <input type="hidden" name="service" value="{{ $service->id }}"/>
                    <div class="form-group">
                        <label for="buried_name">Name</label>
                        <input type="text" class="form-control" name="buried_name"
                               placeholder="Nachname, Vorname" value="{{ $funeral->buried_name }}"/>
                    </div>
                    <div class="form-group">
                        <label for="dob">Geburtsdatum</label>
                        <input type="text" class="form-control datepicker" name="dob"
                               value="@if(isset($funeral->dob)){{ $funeral->dob->format('d.m.Y') }}@endif"/>
                    </div>
                    <div class="form-group">
                        <label for="dod">Sterbedatum</label>
                        <input type="text" class="form-control datepicker" name="dod"
                               value="@if(isset($funeral->dod)){{ $funeral->dod->format('d.m.Y') }}@endif"/>
                    </div>
                    <div class="form-group">
                        <label for="buried_address">Adresse</label>
                        <input type="text" class="form-control" name="buried_address"
                               value="{{ $funeral->buried_address }}"/>
                    </div>
                    <div class="form-group">
                        <label for="buried_zip">PLZ</label>
                        <input type="text" class="form-control" name="buried_zip"
                               value="{{ $funeral->buried_zip }}"/>
                    </div>
                    <div class="form-group">
                        <label for="buried_city">Ort</label>
                        <input type="text" class="form-control" name="buried_city"
                               value="{{ $funeral->buried_city }}"/>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="text">Text</label>
                        <input type="text" class="form-control" name="text" value="{{ $funeral->text }}"/>
                    </div>
                    <div class="form-group">
                        <label for="announcement">Abkündigen am</label>
                        <input type="text" class="form-control datepicker" name="announcement"
                               placeholder="tt.mm.jjjj"
                               value="{{$funeral->announcement ? $funeral->announcement->format('d.m.Y') : ''}}"/>
                    </div>
                    <div class="form-group">
                        <label for="type">Bestattungsart</label>
                        <select id="selType" class="form-control" name="type">
                            <option @if($funeral->type == 'Erdbestattung') selected @endif>Erdbestattung
                            </option>
                            <option @if($funeral->type == 'Trauerfeier') selected @endif>Trauerfeier</option>
                            <option @if($funeral->type == 'Trauerfeier mit Urnenbeisetzung') selected @endif>
                                Trauerfeier mit Urnenbeisetzung
                            </option>
                            <option @if($funeral->type == 'Urnenbeisetzung') selected @endif>Urnenbeisetzung
                            </option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="wake">Datum der vorhergehenden Trauerfeier</label>
                        <input id="dateWake" type="text" class="form-control datepicker" name="wake"
                               placeholder="tt.mm.jjjj"
                               value="@if($funeral->type=='Urnenbeisetzung'){{$funeral->wake ? $funeral->wake->format('d.m.Y') : ''}}@endif"
                               disabled/>
                    </div>
                    <div class="form-group">
                        <label for="wake_location">Ort der vorhergehenden Trauerfeier</label>
                        <input id="locWake" type="text" class="form-control" name="wake_location"
                               value="@if($funeral->type=='Urnenbeisetzung'){{ $funeral->wake_location }}@endif"
                               disabled/>
                    </div>
                @endcomponent

            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Angehörige(r) @endslot
                    <div class="form-group">
                        <label for="relative_name">Name</label>
                        <input type="text" class="form-control" name="relative_name"
                               placeholder="Nachname, Vorname" value="{{ $funeral->relative_name }}"/>
                    </div>
                    <div class="form-group">
                        <label for="relative_address">Adresse</label>
                        <input type="text" class="form-control" name="relative_address"
                               value="{{ $funeral->relative_address }}"/>
                    </div>
                    <div class="form-group">
                        <label for="relative_zip">PLZ</label>
                        <input type="text" class="form-control" name="relative_zip"
                               value="{{ $funeral->relative_zip }}"/>
                    </div>
                    <div class="form-group">
                        <label for="relative_city">Ort</label>
                        <input type="text" class="form-control" name="relative_city"
                               value="{{ $funeral->relative_city }}"/>
                    </div>
                    <div class="form-group">
                        <label for="relative_contact_data">Kontakt</label>
                        <textarea class="form-control"
                                  name="relative_contact_data">{{ $funeral->relative_contact_data }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="appointment">Trauergespräch</label>
                        <input type="text" class="form-control datetimepicker" name="appointment"
                               value="@if(isset($funeral->appointment)){{ $funeral->appointment->format('d.m.Y H:i') }}@endif"
                               placeholder="TT.MM.JJJJ HH:MM"/>
                    </div>
                @endcomponent
                @component('components.ui.card')
                    @slot('cardHeader')Kommentare @endslot
                    @include('partials.comments.list', ['owner' => $funeral, 'ownerClass' => 'App\\Funeral'])
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