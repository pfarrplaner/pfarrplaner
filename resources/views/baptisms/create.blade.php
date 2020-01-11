@extends('layouts.app')

@section('title', 'Taufe hinzufügen')

@section('content')
    <form method="post" action="{{ route('baptisms.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Informationen zur Taufe @endslot
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary" id="submit">Hinzufügen</button>
                    @endslot
                    @if(null !== $service)
                        <input type="hidden" name="service" value="{{ $service->id }}"/>
                        <input type="hidden" name="city_id" value="{{ $service->city_id }}"/>
                    @else
                        <div class="form-group">
                            <label for="service">Taufgottesdienst</label>
                            <select name="service" class="form-control fancy-selectize" @if ($errors->has('service'))is-invalid @endif>
                                <option value="">-- noch kein Datum festgelegt --</option>
                                <optgroup label="Taufgottesdienste">
                                    @foreach($baptismalServices as $baptismalService)
                                        <option value="{{$baptismalService->id}}">
                                            {{ $baptismalService->day->date->format('d.m.Y') }}
                                            , {{ $baptismalService->timeText() }}
                                            ({{ $baptismalService->locationText() }})
                                            [{{ $baptismalService->participantsText('P') }}]
                                            @if(isset($baptismalService->baptisms) && (count($baptismalService->baptisms) >0 ))
                                                -->
                                                bereits {{ count($baptismalService->baptisms) }} @if(count($baptismalService->baptisms) > 1)
                                                    Taufen @else Taufe @endif eingetragen @endif
                                        </option>
                                    @endforeach
                                </optgroup>

                                <optgroup label="Andere Gottesdienste">
                                    @foreach($otherServices as $baptismalService)
                                        <option value="{{$baptismalService->id}}">
                                            {{ $baptismalService->day->date->format('d.m.Y') }}
                                            , {{ $baptismalService->timeText() }}
                                            ({{ $baptismalService->locationText() }})
                                            [{{ $baptismalService->participantsText('P') }}]
                                            @if(isset($baptismalService->baptisms) && (count($baptismalService->baptisms) >0 ))
                                                -->
                                                bereits {{ count($baptismalService->baptisms) }} @if(count($baptismalService->baptisms) > 1)
                                                    Taufen @else Taufe @endif eingetragen @endif
                                        </option>
                                    @endforeach
                                </optgroup>
                            </select>
                            @if($errors->has('service'))
                                @foreach($errors->get('service') as $message)
                                    <div class="invalid-feedback">{!! $message !!}</div>
                                @endforeach
                            @endif
                        </div>
                    @endif
                    @if(null === $service)
                        @selectize(['name' => 'city_id', 'label' => 'Kirchengemeinde', 'items' => $cities, 'id' => 'citySelect'])
                    @endif
                    @input(['name' => 'candidate_name', 'label' => 'Name des Täuflings', 'required' => 1, 'placeholder' => 'Nachname, Vorname'])
                    @input(['name' => 'candidate_address', 'label' => 'Adresse'])
                    @input(['name' => 'candidate_zip', 'label' => 'PLZ', 'pattern' => '^([0]{1}[1-9]{1}|[1-9]{1}[0-9]{1})[0-9]{3}$'])
                    @input(['name' => 'candidate_city', 'label' => 'Ort'])
                    @input(['name' => 'candidate_phone', 'label' => 'Telefon'])
                    @input(['name' => 'candidate_email', 'label' => 'E-Mail'])
                @endcomponent

            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Vorbereitung @endslot
                    @input(['name' => 'first_contact_with', 'label' => 'Erstkontakt mit'])
                    @input(['name' => 'first_contact_on', 'label' => 'Erstkontakt am', 'placeholder' => 'tt.mm.jjjj', 'value' => date('d.m.Y'), 'class' => 'datepicker'])
                    @datetimepicker(['name' => 'appointment', 'label' => 'Taufgespräch', 'placeholder' => 'tt.mm.jjjj HH:MM'])
                    @checkbox(['name' => 'registered', 'label' => 'Anmeldung erhalten'])
                @endcomponent
                @component('components.ui.card')
                    @slot('cardHeader')Dokumente @endslot
                    @upload(['name' => 'registration_document', 'label' => 'PDF des Anmeldedokuments'])
                    @checkbox(['name' => 'signed', 'label' => 'Anmeldung unterschrieben'])
                    @checkbox(['name' => 'docs_ready', 'label' => 'Urkunden gedruckt'])
                    @input(['name' => 'docs_where', 'label' => 'Wo sind die Urkunden hinterlegt?'])
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

        $(document).ready(function () {
            var originalCity = $('#citySelect select').first().val();
            setCitySelect();

            $('select[name=service]').change(function () {
                setCitySelect();
            });
        });
    </script>
@endsection
