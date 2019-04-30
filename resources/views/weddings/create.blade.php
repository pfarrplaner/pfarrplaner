@extends('layouts.app')

@section('title', 'Trauung hinzufügen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Taufe am {{ $service->day->date->format('d.m.Y') }} hinzufügen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('weddings.store') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="service" value="{{ $service->id }}" />
                    <h3>Ehepartner 1</h3>
                    <div class="form-group">
                        <label for="spouse1_name">Name</label>
                        <input type="text" class="form-control" name="spouse1_name" placeholder="Nachname, Vorname"/>
                    </div>
                    <div class="form-group">
                        <label for="spouse1_birth_name">evtl. Geburtsname</label>
                        <input type="text" class="form-control" name="spouse1_birth_name" placeholder="Nachname"/>
                    </div>
                    <div class="form-group">
                        <label for="spouse1_phone">Telefon</label>
                        <input type="text" class="form-control" name="spouse1_phone" />
                    </div>
                    <div class="form-group">
                        <label for="spouse1_email">E-Mail</label>
                        <input type="text" class="form-control" name="spouse1_email" />
                    </div>
                    <hr />
                    <h3>Ehepartner 2</h3>
                    <div class="form-group">
                        <label for="spouse2_name">Name</label>
                        <input type="text" class="form-control" name="spouse2_name" placeholder="Nachname, Vorname"/>
                    </div>
                    <div class="form-group">
                        <label for="spouse2_birth_name">evtl. Geburtsname</label>
                        <input type="text" class="form-control" name="spouse2_birth_name" placeholder="Nachname"/>
                    </div>
                    <div class="form-group">
                        <label for="spouse2_phone">Telefon</label>
                        <input type="text" class="form-control" name="spouse2_phone" />
                    </div>
                    <div class="form-group">
                        <label for="spouse2_email">E-Mail</label>
                        <input type="text" class="form-control" name="spouse2_email" />
                    </div>
                    <hr />
                    <h3>Vorbereitung</h3>
                    <div class="form-group">
                        <label for="appointment">Taufgespräch</label>
                        <input type="text" class="form-control datepicker" name="appointment" placeholder="tt.mm.jjjj" />
                    </div>
                    <div class="form-group">
                        <label for="text">Trautext</label>
                        <input type="text" class="form-control" name="text" />
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
                        <label for="registration_document">PDF des Anmeldedokuments</label>
                        <input type="file" name="registration_document" class="form-control" />
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
                        <input type="text" class="form-control" name="docs_where" />
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-primary" id="submit">Hinzufügen</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
