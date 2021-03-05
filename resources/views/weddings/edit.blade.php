@extends('layouts.app')

@section('title', 'Trauung bearbeiten')

@section('content')
    <form method="post" action="{{ route('weddings.update', $wedding->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row">
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Informationen zur Trauung @endslot
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary" id="submit">Speichern</button>
                    @endslot
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="spouse1_name">Name</label>
                                <input type="text" class="form-control" name="spouse1_name"
                                       placeholder="Nachname, Vorname"
                                       value="{{ $wedding->spouse1_name }}"/>
                            </div>
                            @radiogroup(['name' => 'pronoun_set1', 'label' => 'Zu verwendendes Pronomen', 'items' => \App\Liturgy\PronounSets\PronounSets::items(), 'value' => $wedding->pronoun_set1])
                            <div class="form-group">
                                <label for="spouse1_birth_name">evtl. Geburtsname</label>
                                <input type="text" class="form-control" name="spouse1_birth_name"
                                       placeholder="Nachname"
                                       value="{{ $wedding->spouse1_birth_name }}"/>
                            </div>
                            <div class="form-group">
                                <label for="spouse1_phone">Telefon</label>
                                <input type="text" class="form-control" name="spouse1_phone"
                                       value="{{ $wedding->spouse1_phone }}"/>
                            </div>
                            <div class="form-group">
                                <label for="spouse1_email">E-Mail</label>
                                <input type="text" class="form-control" name="spouse1_email"
                                       value="{{ $wedding->spouse1_email }}"/>
                            </div>

                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="spouse2_name">Name</label>
                                <input type="text" class="form-control" name="spouse2_name"
                                       placeholder="Nachname, Vorname"
                                       value="{{ $wedding->spouse2_name }}"/>
                            </div>
                            @radiogroup(['name' => 'pronoun_set2', 'label' => 'Zu verwendendes Pronomen', 'items' => \App\Liturgy\PronounSets\PronounSets::items(), 'value' => $wedding->pronoun_set2])
                            <div class="form-group">
                                <label for="spouse2_birth_name">evtl. Geburtsname</label>
                                <input type="text" class="form-control" name="spouse2_birth_name"
                                       placeholder="Nachname"
                                       value="{{ $wedding->spouse2_birth_name }}"/>
                            </div>
                            <div class="form-group">
                                <label for="spouse2_phone">Telefon</label>
                                <input type="text" class="form-control" name="spouse2_phone"
                                       value="{{ $wedding->spouse2_phone }}"/>
                            </div>
                            <div class="form-group">
                                <label for="spouse2_email">E-Mail</label>
                                <input type="text" class="form-control" name="spouse2_email"
                                       value="{{ $wedding->spouse2_email }}"/>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="service" value="{{ $wedding->service_id }}"/>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Vorbereitung @endslot
                    @datetimepicker(['name' =>'appointment', 'label' => 'Traugespräch', 'placeholder' => 'TT.MM.JJJJ HH:MM', 'value' => $wedding->appointment ? $wedding->appointment->format('d.m.Y H:i') : ''])
                    <div class="form-group">
                        <label for="text">Trautext</label>
                        <input type="text" class="form-control" name="text" value="{{ $wedding->text }}"/>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="registered" value="1" autocomplete="off"
                                   @if($wedding->registered) checked @endif>
                            <label class="form-check-label">
                                Anmeldung erhalten
                            </label>
                        </div>
                    </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="signed" value="1" autocomplete="off"
                                       @if($wedding->signed) checked @endif>
                                <label class="form-check-label">
                                    Anmeldung unterschrieben
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="docs_ready" value="1" autocomplete="off"
                                       @if($wedding->docs_ready) checked @endif>
                                <label class="form-check-label">
                                    Urkunden gedruckt
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="docs_where">Wo sind die Urkunden hinterlegt?</label>
                            <input type="text" class="form-control" name="docs_where"
                                   value="{{ $wedding->docs_where }}"/>
                        </div>
                @endcomponent
                @include('components.attachments', ['object' => $wedding])
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @component('components.ui.card')
                    @slot('cardHeader')Kommentare @endslot
                    @include('partials.comments.list', ['owner' => $wedding, 'ownerClass' => 'App\\Wedding'])
                @endcomponent
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>var attachments = {{ count($wedding->attachments) }};</script>
    <script src="{{ asset('js/pfarrplaner/attachments.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <script>
        var commentRoute = '{{ route('comments.store') }}';
        var commentOwner = '{{ $wedding->id }}';
        var commentOwnerClass = 'App\\Wedding';
    </script>
    <script src="{{ asset('js/pfarrplaner/comments.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#btnRemoveAttachment').click(function () {
                $('#linkToAttachment').after('<input type="file" name="registration_document" class="form-control" /><input type="hidden" name="removeAttachment" value="1" />');
                $('#linkToAttachment').hide();
                $('#btnRemoveAttachment').hide();
            });
        });
    </script>
@endsection

