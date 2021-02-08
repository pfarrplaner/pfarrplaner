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
                    @hidden(['name' => 'service_id', 'value' => $service->id])
                    @input(['name' => 'buried_name', 'label'=> 'Name', 'placeholder' => 'Nachname, Vorname', 'value' => $funeral->buried_name])
                    @radiogroup(['name' => 'pronoun_set', 'label' => 'Zu verwendendes Pronomen', 'items' => \App\Liturgy\PronounSets\PronounSets::items(), 'value' => $funeral->pronoun_set])
                    @input(['name' => 'dob', 'label'=> 'Geburtsdatum', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker', 'value' => (is_object($funeral->dob) ? $funeral->dob->format('d.m.Y') : '')])
                    @input(['name' => 'dod', 'label'=> 'Sterbedatum', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker', 'value' => (is_object($funeral->dod) ? $funeral->dod->format('d.m.Y') : '')])
                    @input(['name' => 'buried_address', 'label'=> 'Adresse', 'value' => $funeral->buried_address])
                    @input(['name' => 'buried_zip', 'label'=> 'PLZ', 'value' => $funeral->buried_zip])
                    @input(['name' => 'buried_city', 'label'=> 'Ort', 'value' => $funeral->buried_city])
                    <hr/>
                    @input(['name' => 'text', 'label'=> 'Text', 'value' => $funeral->text])
                    @input(['name' => 'announcement', 'label'=> 'Abkündigen am', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker', 'value' => (is_object($funeral->announcement) ? $funeral->announcement->format('d.m.Y') : '')])
                    @select(['name' => 'type', 'label' => 'Bestattungsart', 'items' => ['Erdbestattung', 'Trauerfeier', 'Trauerfeier mit Urnenbeisetzung', 'Urnenbeisetzung'], 'id' => 'selType', 'value' => $funeral->type])
                    @input(['name' => 'wake', 'label'=> 'Datum der vorhergehenden Trauerfeier', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker', 'id' => 'dateWake', 'value' => (is_object($funeral->wake) ? $funeral->wake->format('d.m.Y') : '')])
                    @input(['name' => 'wake_location', 'label'=> 'Ort der vorhergehenden Trauerfeier', 'id' => 'locWake', 'value' => $funeral->wake_location])
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Angehörige(r) @endslot
                    @input(['name' => 'relative_name', 'label'=> 'Name', 'placeholder' => 'Nachname, Vorname', 'value' => $funeral->relative_name])
                    <a class="btn btn-sm btn-secondary" href="#" title="Adresse übernehmen"><span class="fa fa-copy"></span> Adresse übernehmen</a>
                    @input(['name' => 'relative_address', 'label'=> 'Adresse', 'value' => $funeral->relative_address])
                    @input(['name' => 'relative_zip', 'label'=> 'PLZ', 'value' => $funeral->relative_zip])
                    @input(['name' => 'relative_city', 'label'=> 'Ort', 'value' => $funeral->relative_city])
                    @textarea(['name' => 'relative_contact_data', 'label' => 'Kontakt', 'value' => $funeral->relative_contact_data])
                    @datetimepicker(['name' => 'appointment', 'label' => 'Trauergespräch', 'placeholder' => 'TT.MM.JJJJ hh:mm', 'value' => (is_object($funeral->appointment) ? $funeral->appointment->format('d.m.Y H:i') : '')])
                @endcomponent
                @include('components.attachments', ['object' => $funeral])
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @component('components.ui.card')
                    @slot('cardHeader')Kommentare @endslot
                    @include('partials.comments.list', ['owner' => $funeral, 'ownerClass' => 'App\\Funeral'])
                @endcomponent
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>var attachments = {{ count($funeral->attachments) }};</script>
    <script src="{{ asset('js/pfarrplaner/attachments.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <script>
        var commentRoute = '{{ route('comments.store') }}';
        var commentOwner = '{{ $funeral->id }}';
        var commentOwnerClass = 'App\\Funeral';
    </script>
    <script src="{{ asset('js/pfarrplaner/comments.js') }}"></script>
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

            $('#copyAddress').click((e) => {
                e.preventDefault();
                $('input[name=relative_address]').val($('input[name=buried_address]').val());
                $('input[name=relative_zip]').val($('input[name=buried_zip]').val());
                $('input[name=relative_city]').val($('input[name=buried_city]').val());
            });
        });
    </script>
@endsection
