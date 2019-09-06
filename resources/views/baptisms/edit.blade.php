@extends('layouts.app')

@section('title', 'Taufe bearbeiten')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Taufe bearbeiten
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('baptisms.update', $baptism->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')


                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" href="#home" role="tab" data-toggle="tab">Allgemeines</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#comments" role="tab" data-toggle="tab">Kommentare <span class="badge badge-primary" id="commentCount">{{ $baptism->commentsForCurrentUser->count() }}</span></a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <br />

                        <div role="tabpanel" class="tab-pane fade in active show" id="home">
                            <div class="form-group">
                                <label for="service">Taufgottesdienst</label>
                                <select name="service" class="form-control">
                                    <option value="">-- noch kein Datum festgelegt --</option>
                                        <optgroup label="Taufgottesdienste">
                                            @foreach($baptismalServices as $baptismalService)
                                            <option value="{{$baptismalService->id}}" @if($baptism->service_id == $baptismalService->id) selected @endif data-city="{{ $baptismalService->city_id }}">
                                                {{ $baptismalService->day->date->format('d.m.Y') }}, {{ $baptismalService->timeText() }} ({{ $baptismalService->locationText() }}) P: {{ $baptismalService->participantsText('P') }} @if ($baptismalService->baptisms->count()) [bisherige Taufen: {{ $baptismalService->baptisms->count() }}] @endif
                                            </option>
                                            @endforeach
                                        </optgroup>

                                        <optgroup label="Andere Gottesdienste">
                                            @foreach($otherServices as $baptismalService)
                                                <option value="{{$baptismalService->id}}" @if($baptism->service_id == $baptismalService->id) selected @endif data-city="{{ $baptismalService->city_id }}">
                                                    {{ $baptismalService->day->date->format('d.m.Y') }}, {{ $baptismalService->timeText() }} ({{ $baptismalService->locationText() }}) P: {{ $baptismalService->participantsText('P') }} @if ($baptismalService->baptisms->count()) [bisherige Taufen: {{ $baptismalService->baptisms->count() }}] @endif
                                                </option>
                                            @endforeach
                                        </optgroup>
                                </select>
                            </div>
                            <div id="citySelect" class="form-group" @if (null !== $baptism->service_id) style="display:none;" @endif>
                                <label for="city_id">Kirchengemeinde</label>
                                <select class="form-control" name="city_id">
                                    @foreach ($cities as $city)
                                        <option value="{{ $city->id }}" @if ($baptism->city_id == $city->id) selected @endif>{{ $city->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <input id="cityHidden" type="hidden" name="city_id" value="{{ $baptism->city_id }}" />
                            <div class="form-group">
                                <label for="candidate_name">Name des Täuflings</label>
                                <input type="text" class="form-control" name="candidate_name" placeholder="Nachname, Vorname" value="{{ $baptism->candidate_name }}"/>
                            </div>
                            <div class="form-group">
                                <label for="candidate_address">Adresse</label>
                                <input type="text" class="form-control" name="candidate_address"  value="{{ $baptism->candidate_address }}"/>
                            </div>
                            <div class="form-group">
                                <label for="candidate_zip">PLZ</label>
                                <input type="text" class="form-control" name="candidate_zip"  value="{{ $baptism->candidate_zip }}"/>
                            </div>
                            <div class="form-group">
                                <label for="candidate_city">Ort</label>
                                <input type="text" class="form-control" name="candidate_city"  value="{{ $baptism->candidate_city }}"/>
                            </div>
                            <div class="form-group">
                                <label for="candidate_phone">Telefon</label>
                                <input type="text" class="form-control" name="candidate_phone"  value="{{ $baptism->candidate_phone }}"/>
                            </div>
                            <div class="form-group">
                                <label for="candidate_email">E-Mail</label>
                                <input type="text" class="form-control" name="candidate_email"  value="{{ $baptism->candidate_email }}"/>
                            </div>
                            <div class="form-group">
                                <label for="first_contact_with">Erstkontakt mit</label>
                                <input type="text" class="form-control" name="first_contact_with" value="{{ $baptism->first_contact_with }}" />
                            </div>
                            <div class="form-group">
                                <label for="first_contact_on">Datum des Erstkontakts</label>
                                <input type="text" class="form-control datepicker" name="first_contact_on" placeholder="tt.mm.jjjj"  value="{{ $baptism->first_contact_on ? $baptism->first_contact_on->format('d.m.Y') : ''}}"/>
                            </div>
                            <div class="form-group ">
                                <label for="appointment">Taufgespräch</label>
                                <input type="text" class="form-control datetimepicker datetimepicker-input" id="appointmentpicker" data-toggle="datetimepicker" data-target="#appointmentpicker" name="appointment" placeholder="tt.mm.jjjj HH:MM"  value="{{ $baptism->appointment ? $baptism->appointment->format('d.m.Y H:i') : ''}}"/>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="registered" value="1" autocomplete="off" @if($baptism->registered) checked @endif>
                                    <label class="form-check-label">
                                        Anmeldung erhalten
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="registration_document">PDF des Anmeldedokuments</label>
                                @if ($baptism->registration_document)
                                    <a id="linkToAttachment" href="{{ env('APP_URL').'storage/'.$baptism->registration_document }}">{{ $baptism->registration_document }}</a>
                                    <a class="btn btn-sm btn-danger" id="btnRemoveAttachment" title="Dokumentanhang entfernen"><span class="fa fa-trash"></span></a>
                                @else
                                <input type="file" name="registration_document" class="form-control" />
                                @endif
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="signed" value="1" autocomplete="off" @if($baptism->signed) checked @endif>
                                    <label class="form-check-label">
                                        Anmeldung unterschrieben
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="form-check">
                                    <input type="checkbox" name="docs_ready" value="1" autocomplete="off" @if($baptism->docs_ready) checked @endif>
                                    <label class="form-check-label">
                                        Urkunden gedruckt
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="docs_where">Wo sind die Urkunden hinterlegt?</label>
                                <input type="text" class="form-control" name="docs_where"  value="{{ $baptism->docs_where }}" />
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="comments">
                            @include('partials.comments.list', ['owner' => $baptism, 'ownerClass' => 'App\\Baptism'])
                        </div>
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-primary" id="submit">Speichern</button>
                </form>
            </div>
        </div>
        <script>
            $(document).ready(function(){
                $('#btnRemoveAttachment').click(function(){
                    $('#linkToAttachment').after('<input type="file" name="registration_document" class="form-control" /><input type="hidden" name="removeAttachment" value="1" />');
                    $('#linkToAttachment').hide();
                    $('#btnRemoveAttachment').hide();
                });

                var originalCity = $('#citySelect select').first().val();

                $('select[name=service]').change(function(){
                    if ($(this).val() == '') {
                        $('#citySelect select').first().val(originalCity);
                        $('#cityHidden').val(orginalCity);
                        $('#citySelect').show();
                        $('#citySelect select').first().focus();
                    } else {
                        $('#citySelect').hide();
                        $('#citySelect select').val($(this).find(':selected').data('city'));
                        $('#cityHidden').val($(this).find(':selected').data('city'));
                        $('input[name=candidate_name]').focus();

                    }
                });

                $('#citySelect select').change(function(){
                   $('#cityHidden').val($(this).val());
                });


            });
        </script>
    @endcomponent
@endsection
