@extends('layouts.app')

@section('title', 'Taufe hinzufügen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Taufe hinzufügen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('baptisms.store') }}" enctype="multipart/form-data">
                    @csrf
                    @if(null !== $service)
                        <input type="hidden" name="service" value="{{ $service->id }}" />
                        <input type="hidden" name="city_id" value="{{ $service->city_id }}" />
                    @else
                        <div class="form-group">
                            <label for="service">Taufgottesdienst</label>
                            <select name="service" class="form-control">
                                <option value="">-- noch kein Datum festgelegt --</option>
                                <optgroup label="Taufgottesdienste">
                                    @foreach($baptismalServices as $baptismalService)
                                        <option value="{{$baptismalService->id}}">
                                            {{ $baptismalService->day->date->format('d.m.Y') }}, {{ $baptismalService->timeText() }} ({{ $baptismalService->locationText() }})
                                        </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Andere Gottesdienste">
                                    @foreach($otherServices as $baptismalService)
                                        <option value="{{$baptismalService->id}}">
                                            {{ $baptismalService->day->date->format('d.m.Y') }}, {{ $baptismalService->timeText() }} ({{ $baptismalService->locationText() }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                    @endif
                    <div id="citySelect" class="form-group" @if (null !== $service) style="display:none;" @endif>
                        <label for="city_id">Kirchengemeinde</label>
                        <select class="form-control" name="city_id">
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="candidate_name">Name des Täuflings</label>
                        <input type="text" class="form-control" name="candidate_name" placeholder="Nachname, Vorname"/>
                    </div>
                    <div class="form-group">
                        <label for="candidate_address">Adresse</label>
                        <input type="text" class="form-control" name="candidate_address" />
                    </div>
                    <div class="form-group">
                        <label for="candidate_zip">PLZ</label>
                        <input type="text" class="form-control" name="candidate_zip" />
                    </div>
                    <div class="form-group">
                        <label for="candidate_city">Ort</label>
                        <input type="text" class="form-control" name="candidate_city" />
                    </div>
                    <div class="form-group">
                        <label for="candidate_phone">Telefon</label>
                        <input type="text" class="form-control" name="candidate_phone" />
                    </div>
                    <div class="form-group">
                        <label for="candidate_email">E-Mail</label>
                        <input type="text" class="form-control" name="candidate_email" />
                    </div>
                    <div class="form-group">
                        <label for="first_contact_with">Erstkontakt mit</label>
                        <input type="text" class="form-control" name="first_contact_with" value="{{ Auth::user()->name }}" />
                    </div>
                    <div class="form-group">
                        <label for="first_contact_on">Datum des Erstkontakts</label>
                        <input type="text" class="form-control datepicker" name="first_contact_on" placeholder="tt.mm.jjjj" value="{{ date('d.m.Y') }}"/>
                    </div>
                    <div class="form-group">
                        <label for="appointment">Taufgespräch</label>
                        <input type="text" class="form-control datepicker" name="appointment" placeholder="tt.mm.jjjj" />
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
        <script>
            function setCitySelect() {
                if ($('select[name=service]').val() == '') {
                    $('#citySelect select').first().val(originalCity);
                    $('#citySelect').show();
                    $('#citySelect select').first().focus();
                } else {
                    $('#citySelect').hide();
                    $('#citySelect select').val($(this).data('city'));
                    $('input[name=candidate_name]').focus();
                }
            }

            $(document).ready(function(){
                var originalCity = $('#citySelect select').first().val();
                setCitySelect();

                $('select[name=service]').change(function(){
                    setCitySelect();
                });
            });
        </script>
    @endcomponent
@endsection
