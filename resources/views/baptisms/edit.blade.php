@extends('layouts.app')

@section('title', 'Taufe bearbeiten')

@section('content')
    <form method="post" action="{{ route('baptisms.update', $baptism->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="row">
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Informationen zur Taufe @endslot
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary" id="submit">Speichern</button>
                    @endslot
                    <div class="form-group">
                        <label for="service">Taufgottesdienst</label>
                        <select name="service" class="form-control @if ($errors->has('service'))is-invalid @endif">
                            <option value="">-- noch kein Datum festgelegt --</option>
                            <optgroup label="Taufgottesdienste">
                                @foreach($baptismalServices as $baptismalService)
                                    <option value="{{$baptismalService->id}}"
                                            @if($baptism->service_id == $baptismalService->id) selected
                                            @endif data-city="{{ $baptismalService->city_id }}">
                                        {{ $baptismalService->day->date->format('d.m.Y') }}
                                        , {{ $baptismalService->timeText() }}
                                        ({{ $baptismalService->locationText() }})
                                        P: {{ $baptismalService->participantsText('P') }} @if ($baptismalService->baptisms->count())
                                            [bisherige Taufen: {{ $baptismalService->baptisms->count() }}] @endif
                                    </option>
                                @endforeach
                            </optgroup>

                            <optgroup label="Andere Gottesdienste">
                                @foreach($otherServices as $baptismalService)
                                    <option value="{{$baptismalService->id}}"
                                            @if($baptism->service_id == $baptismalService->id) selected
                                            @endif data-city="{{ $baptismalService->city_id }}">
                                        {{ $baptismalService->day->date->format('d.m.Y') }}
                                        , {{ $baptismalService->timeText() }}
                                        ({{ $baptismalService->locationText() }})
                                        P: {{ $baptismalService->participantsText('P') }} @if ($baptismalService->baptisms->count())
                                            [bisherige Taufen: {{ $baptismalService->baptisms->count() }}] @endif
                                    </option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                    @if(!isset($service))
                        @selectize(['name' => 'city_id', 'label' => 'Kirchengemeinde', 'items' => $cities, 'id' => 'citySelect', 'value' => $baptism->city_id])
                    @endif
                    @hidden(['name' => 'city_id', 'id' => 'cityHidden', 'value' => $baptism->city_id])
                    @input(['name' => 'candidate_name', 'label' => 'Name des Täuflings', 'required' => 1, 'placeholder' => 'Nachname, Vorname', 'value' => $baptism->candidate_name])
                    @input(['name' => 'candidate_address', 'label' => 'Adresse', 'value' => $baptism->candidate_address])
                    @input(['name' => 'candidate_zip', 'label' => 'PLZ', 'pattern' => '^([0]{1}[1-9]{1}|[1-9]{1}[0-9]{1})[0-9]{3}$', 'value' => $baptism->candidate_zip])
                    @input(['name' => 'candidate_city', 'label' => 'Ort', 'value' => $baptism->candidate_city])
                    @input(['name' => 'candidate_phone', 'label' => 'Telefon', 'value' => $baptism->candidate_phone])
                    @input(['name' => 'candidate_email', 'label' => 'E-Mail', 'value' => $baptism->candidate_email])
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Vorbereitung @endslot
                        @slot('cardHeader')Vorbereitung @endslot
                        @input(['name' => 'first_contact_with', 'label' => 'Erstkontakt mit', 'value' => $baptism->first_contact_with])
                        @input(['name' => 'first_contact_on', 'label' => 'Erstkontakt am', 'placeholder' => 'tt.mm.jjjj', 'value' => date('d.m.Y'), 'class' => 'datepicker', 'value' => (is_object($baptism->first_contact) ? $baptism->first_contact_on->format('d.m.Y')  : '')])
                        @datetimepicker(['name' => 'appointment', 'label' => 'Taufgespräch', 'placeholder' => 'tt.mm.jjjj HH:MM', 'value' => (is_object($baptism->appointment) ? $baptism->appointment->format('d.m.Y H:i') : '')])
                        @checkbox(['name' => 'registered', 'label' => 'Anmeldung erhalten', 'value' => $baptism->registered])
                @endcomponent
                @component('components.ui.card')
                    @slot('cardHeader')Dokumente @endslot
                        @slot('cardHeader')Dokumente @endslot
                        @upload(['name' => 'registration_document', 'label' => 'PDF des Anmeldedokuments', 'value' => $baptism->registration_document])
                        @checkbox(['name' => 'signed', 'label' => 'Anmeldung unterschrieben', 'value' => $baptism->signed])
                        @checkbox(['name' => 'docs_ready', 'label' => 'Urkunden gedruckt', 'value' => $baptism->docs_ready])
                        @input(['name' => 'docs_where', 'label' => 'Wo sind die Urkunden hinterlegt?', 'value' => $baptism->docs_where])
                @endcomponent
                @include('components.attachments', ['object' => $baptism])
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                @component('components.ui.card')
                    @slot('cardHeader')Kommentare @endslot
                    @include('partials.comments.list', ['owner' => $baptism, 'ownerClass' => 'App\\Baptism'])
                @endcomponent
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>var attachments = {{ count($baptism->attachments) }};</script>
    <script src="{{ asset('js/pfarrplaner/attachments.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#btnRemoveAttachment').click(function () {
                $('#linkToAttachment').after('<input type="file" name="registration_document" class="form-control" /><input type="hidden" name="removeAttachment" value="1" />');
                $('#linkToAttachment').hide();
                $('#btnRemoveAttachment').hide();
            });

            var originalCity = $('#citySelect select').first().val();

            $('select[name=service]').change(function () {
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

            $('#citySelect select').change(function () {
                $('#cityHidden').val($(this).val());
            });


        });
    </script>
@endsection
