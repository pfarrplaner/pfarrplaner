@extends('layouts.app')

@section('title')Trauung am {{ $service->day->date->format('d.m.Y') }} hinzufügen @endsection

@section('content')
    <form method="post" action="{{ route('weddings.store') }}" enctype="multipart/form-data">
        @csrf
        @hidden(['name' => 'service', 'value' => $service->id])
        <div class="row">
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Ehepartner @endslot
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary" id="submit">Hinzufügen</button>
                    @endslot
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="spouse1_name">Name</label>
                                <input type="text" class="form-control" name="spouse1_name" placeholder="Nachname, Vorname"/>
                            </div>
                            @radiogroup(['name' => 'pronoun_set1', 'label' => 'Zu verwendendes Pronomen', 'items' => \App\Liturgy\PronounSets\PronounSets::items()])
                            <div class="form-group">
                                <label for="spouse1_birth_name">evtl. Geburtsname</label>
                                <input type="text" class="form-control" name="spouse1_birth_name" placeholder="Nachname"/>
                            </div>
                            <div class="form-group">
                                <label for="spouse1_phone">Telefon</label>
                                <input type="text" class="form-control" name="spouse1_phone"/>
                            </div>
                            <div class="form-group">
                                <label for="spouse1_email">E-Mail</label>
                                <input type="text" class="form-control" name="spouse1_email"/>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="spouse2_name">Name</label>
                                <input type="text" class="form-control" name="spouse2_name" placeholder="Nachname, Vorname"/>
                            </div>
                            @radiogroup(['name' => 'pronoun_set2', 'label' => 'Zu verwendendes Pronomen', 'items' => \App\Liturgy\PronounSets\PronounSets::items()])
                            <div class="form-group">
                                <label for="spouse2_birth_name">evtl. Geburtsname</label>
                                <input type="text" class="form-control" name="spouse2_birth_name" placeholder="Nachname"/>
                            </div>
                            <div class="form-group">
                                <label for="spouse2_phone">Telefon</label>
                                <input type="text" class="form-control" name="spouse2_phone"/>
                            </div>
                            <div class="form-group">
                                <label for="spouse2_email">E-Mail</label>
                                <input type="text" class="form-control" name="spouse2_email"/>
                            </div>
                        </div>
                    </div>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Vorbereitung @endslot
                    <div class="form-group">
                        <label for="appointment">Traugespräch</label>
                        <input type="text" class="form-control datepicker" name="appointment" placeholder="tt.mm.jjjj"/>
                    </div>
                    <div class="form-group">
                        <label for="text">Trautext</label>
                        <input type="text" class="form-control" name="text"/>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="registered" value="1" autocomplete="off">
                            <label class="form-check-label">
                                Anmeldung erhalten
                            </label>
                        </div>
                    </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="signed" value="1" autocomplete="off">
                                <label class="form-check-label">
                                    Anmeldung unterschrieben
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="form-check">
                                <input type="checkbox" name="docs_ready" value="1" autocomplete="off">
                                <label class="form-check-label">
                                    Urkunden gedruckt
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="docs_where">Wo sind die Urkunden hinterlegt?</label>
                            <input type="text" class="form-control" name="docs_where"/>
                        </div>
                @endcomponent
                @include('components.attachments')
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script>var attachments = 0;</script>
    <script src="{{ asset('js/pfarrplaner/attachments.js') }}"></script>
@endsection
