@extends('layouts.app')

@section('title', 'Gottesdienst bearbeiten')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Gottesdiensteintrag bearbeiten
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form id="frmEdit" method="post" action="{{ route('services.update', $service->id) }}">
                    @method('PATCH')
                    @csrf

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item active">
                            <a class="nav-link active" href="#home" role="tab" data-toggle="tab">Allgemeines</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#special" role="tab" data-toggle="tab">Besonderheiten</a>
                        </li>
                        @canany(['gd-opfer-lesen','gd-opfer-bearbeiten'])
                        <li class="nav-item">
                            <a class="nav-link" href="#offerings" role="tab" data-toggle="tab">Opfer</a>
                        </li>
                        @endcanany
                        @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten', 'gd-kasualien-nur-statistik'])
                        <li class="nav-item">
                            <a class="nav-link" href="#rites" role="tab" data-toggle="tab">Kasualien</a>
                        </li>
                        @endcan
                        @canany(['gd-kinderkirche-lesen', 'gd-kinderkirche-bearbeiten'])
                        <li class="nav-item">
                            <a class="nav-link" href="#cc" role="tab" data-toggle="tab">Kinderkirche</a>
                        </li>
                        @endcanany
                        @can('admin')
                        <li class="nav-item">
                            <a class="nav-link" href="#history" role="tab" data-toggle="tab">Bearbeitungen</a>
                        </li>
                        @endcan('admin')
                    </ul>

                    <div class="tab-content">
                        <br />
                        <div role="tabpanel" class="tab-pane fade in active show" id="home">
                            <div class="form-group">
                                <label for="day_id">Datum</label>
                                <select class="form-control" name="day_id" @cannot('gd-allgemein-bearbeiten') disabled @endcannot >
                                    @foreach($days as $thisDay)
                                        <option value="{{$thisDay->id}}"
                                                @if ($service->day->id == $thisDay->id) selected @endif
                                        >{{$thisDay->date->format('d.m.Y')}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="location_id">Kirche</label>
                                <select class="form-control" name="location_id" @cannot('gd-allgemein-bearbeiten') disabled @endcannot >
                                    @foreach($locations as $thisLocation)
                                        <option data-time="{{ strftime('%H:%M', strtotime($thisLocation->default_time)) }}"
                                                value="{{$thisLocation->id}}"
                                                @if (is_object($service->location))
                                                @if ($service->location->id == $thisLocation->id) selected @endif
                                            @endif
                                        >
                                            {{$thisLocation->name}}
                                        </option>
                                    @endforeach
                                    <option value=""
                                            @if (!is_object($service->location)) selected @endif
                                    >Freie Ortsangabe</option>
                                </select>
                            </div>
                            <div id="special_location" class="form-group">
                                <label for="special_location">Freie Ortsangabe</label>
                                <input id="special_location_input" class="form-control" type="text" name="special_location" value="{{ $service->special_location }}"/>
                                <input type="hidden" name="city_id" value="{{ $service->city_id }}"/>
                            </div>
                            <div class="form-group">
                                <label for="time">Uhrzeit (leer lassen für Standarduhrzeit)</label>
                                <input class="form-control" type="text" name="time" placeholder="HH:MM"
                                       value="{{ strftime('%H:%M', strtotime($service->time)) }}" @cannot('gd-allgemein-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="participants[P][]"><span class="fa fa-user"></span>&nbsp;Pfarrer*in</label>
                                <select class="form-control peopleSelect" name="participants[P][]" multiple @cannot('gd-pfarrer-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @if($service->pastors->contains($user)) selected @endif>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="need_predicant" value="1"
                                       id="needPredicant" @if ($service->need_predicant) checked @endif @cannot('gd-pfarrer-bearbeiten') disabled @endcannot >
                                <label class="form-check-label" for="needPredicant">
                                    Für diesen Gottesdienst wird ein Prädikant benötigt.
                                </label>
                                <br /><br />
                            </div>
                            <div class="form-group">
                                <label for="participants[O][]"><span class="fa fa-user"></span>&nbsp;Organist*in</label>
                                <select class="form-control peopleSelect" name="participants[O][]" multiple @cannot('gd-organist-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @if($service->organists->contains($user)) selected @endif>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="participants[M][]"><span class="fa fa-user"></span>&nbsp;Mesner*in</label>
                                <select class="form-control peopleSelect" name="participants[M][]" multiple @cannot('gd-mesner-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @if($service->sacristans->contains($user)) selected @endif>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="participants[A][]"><span class="fa fa-users"></span>&nbsp;Weitere Beteiligte</label>
                                <select class="form-control peopleSelect" name="participants[A][]" multiple @cannot('gd-allgemein-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @if($service->otherParticipants->contains($user)) selected @endif>{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div role="tabpanel" id="special" class="tab-pane fade">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="baptism" value="1"
                                       id="baptism" @if ($service->baptism) checked @endif @cannot('gd-taufe-bearbeiten') disabled @endcannot >
                                <label class="form-check-label" for="baptism">
                                    Taufe(n)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="eucharist" value="1"
                                       id="eucharist" @if ($service->eucharist) checked @endif @cannot('gd-abendmahl-bearbeiten') disabled @endcannot >
                                <label class="form-check-label" for="eucharist">
                                    Abendmahl
                                </label>
                            </div>
                            <br />
                            <div class="form-group">
                                <label for="description">Anmerkungen</label>
                                <input type="text" class="form-control" name="description" value="{{ $service->description }}" @canany(['gd-allgemein-bearbeiten','gd-anmerkungen-bearbeiten']) @else disabled @endcanany/>
                            </div>
                        </div>
                        <div role="tabpanel" id="offerings" class="tab-pane fade">
                            <div class="form-group">
                                <label for="offerings_counter1">Opferzähler*in 1</label>
                                <input class="form-control" type="text" name="offerings_counter1" value="{{ $service->offerings_counter1 }}" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="offerings_counter2">Opferzähler*in 2</label>
                                <input class="form-control" type="text" name="offerings_counter2" value="{{ $service->offerings_counter2 }}" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="offering_goal">Opferzweck</label>
                                <input class="form-control" type="text" name="offering_goal" value="{{ $service->offering_goal }}" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label style="display:block;">Opfertyp</label>
                                <div class="form-check-inline">
                                    <input type="radio" name="offering_type" value="" autocomplete="off" @if($service->offering_type == '')checked @endif @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                                    <label class="form-check-label">
                                        Eigener Beschluss
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <input type="radio" name="offering_type" value="eO" autocomplete="off" @if($service->offering_type == 'eO')checked @endif @cannot('gd-opfer-bearbeiten') disabled @endcannot  />
                                    <label class="form-check-label">
                                        Empfohlenes Opfer
                                    </label>
                                </div>
                                <div class="form-check-inline disabled">
                                    <input type="radio" name="offering_type" value="PO" autocomplete="off" @if($service->offering_type == 'PO')checked @endif @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                                    <label class="form-check-label">
                                        Pflichtopfer
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="offering_description">Anmerkungen zum Opfer</label>
                                <input class="form-control" type="text" name="offering_description" value="{{ $service->offering_description }}" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                            </div>
                        </div>
                        @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten', 'gd-kasualien-nur-statistik'])
                            <div role="tabpanel" class="tab-pane fade" id="rites">
                                @if ($service->weddings->count() >0 )
                                    <h3>{{ $service->weddings->count() }} @if($service->weddings->count() != 1)Trauungen @else Trauung @endif</h3>
                                    @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten'])
                                        <table class="table table-fluid">
                                            <tr>
                                                <th>Ehepartner 1</th>
                                                <th>Ehepartner 2</th>
                                                <th>Traugespräch</th>
                                                <th>Anmeldung</th>
                                                <th>Urkunden</th>
                                                <th></th>
                                            </tr>
                                            @foreach($service->weddings as $wedding)
                                                <tr>
                                                    @include('partials.wedding.details', ['wedding' => $wedding])
                                                    <td>
                                                        @can('gd-kasualien-bearbeiten')
                                                            <a class="btn btn-default btn-secondary" href="{{ route('weddings.edit', $wedding->id) }}" title="Trauung bearbeiten"><span class="fa fa-edit"></span></a>
                                                            <a class="btn btn-default btn-danger" href="{{ route('wedding.destroy', $wedding->id) }}" title="Trauung löschen"><span class="fa fa-trash"></span></a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endcanany
                                @endif
                                @if ($service->baptisms->count() >0 )
                                    <h3>{{ $service->baptisms->count() }} @if($service->baptisms->count() != 1)Taufen @else Taufe @endif</h3>
                                    @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten'])
                                        <table class="table table-fluid">
                                            <tr>
                                                <th>Täufling</th>
                                                <th>Erstkontakt</th>
                                                <th>Taufgespräch</th>
                                                <th>Anmeldung</th>
                                                <th>Urkunden</th>
                                                <th></th>
                                            </tr>
                                            @foreach($service->baptisms as $baptism)
                                                <tr>
                                                    @include('partials.baptism.details', ['baptism' => $baptism])
                                                    <td>
                                                        @can('gd-kasualien-bearbeiten')
                                                            <a class="btn btn-default btn-secondary" href="{{ route('baptisms.edit', $baptism->id) }}" title="Taufe bearbeiten"><span class="fa fa-edit"></span></a>
                                                            <a class="btn btn-default btn-danger" href="{{ route('baptism.destroy', $baptism->id) }}" title="Taufe löschen"><span class="fa fa-trash"></span></a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endcanany
                                @endif
                                @if ($service->funerals->count() > 0)
                                <h3>{{ $service->funerals->count() }} @if($service->funerals->count() != 1)Bestattungen @else Bestattung @endif</h3>
                                    @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten'])
                                        <table class="table table-fluid">
                                            <tr>
                                                <th>Person</th>
                                                <th>Bestattungsart</th>
                                                <th>Abkündigung</th>
                                                <th></th>
                                            </tr>
                                            @foreach ($service->funerals as $funeral)
                                                <tr>
                                                    @include('partials.funeral.details', ['funeral' => $funeral])
                                                    <td>
                                                        @can('gd-kasualien-bearbeiten')
                                                            <a class="btn btn-default btn-secondary" href="{{ route('funerals.edit', $funeral->id) }}" title="Bestattung bearbeiten"><span class="fa fa-edit"></span></a>
                                                            <a class="btn btn-default btn-danger" href="{{ route('funeral.destroy', $funeral->id) }}" title="Bestattung löschen"><span class="fa fa-trash"></span></a>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    @endcanany
                                @endif
                                @can('gd-kasualien-bearbeiten')
                                    <a class="btn btn-default btn-secondary" href="{{ route('wedding.add', $service->id) }}">Trauung hinzufügen</a>
                                    <a class="btn btn-default btn-secondary" href="{{ route('baptism.add', $service->id) }}">Taufe hinzufügen</a>
                                    <a class="btn btn-default btn-secondary" href="{{ route('funeral.add', $service->id) }}">Bestattung hinzufügen</a>
                                @endcan
                            </div>
                        @endcanany
                        <div role="tabpanel" class="tab-pane fade" id="cc">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cc" value="1"
                                       id="cc-check" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
                                <label class="form-check-label" for="cc">
                                    Parallel findet Kinderkirche statt
                                </label>
                            </div>
                            <br />
                            <div class="form-group">
                                <label for="cc_location">Ort der Kinderkirche:</label>
                                <input class="form-control" type="text" name="cc_location" placeholder="Leer lassen für " value="{{ $service->cc_location }}" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="cc_lesson">Lektion:</label>
                                <input class="form-control" type="text" name="cc_lesson" value="{{ $service->cc_lesson }}" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="cc_staff">Mitarbeiter:</label>
                                <input class="form-control" type="text" name="cc_staff" placeholder="Name, Name, ..." value="{{ $service->cc_staff }}" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
                            </div>
                        </div>
                        @can('admin')
                            <div role="tabpanel" id="history" class="tab-pane fade">
                                @if (count($service->revisionHistory))
                                        <b>Bisherige Änderungen an diesem Gottesdiensteintrag:</b><br />
                                        @foreach($service->revisionHistory as $history)
                                            @if($history->key == 'created_at' && !$history->old_value)
                                                {{ $history->created_at->format('d.m.Y, H:i:s') }}
                                                : {{ $history->userResponsible()->name }} hat diesen Eintrag
                                                angelegt: {{ $history->newValue() }}<br/>
                                            @else
                                                {{ $history->created_at->format('d.m.Y, H:i:s') }}
                                                : {{ $history->userResponsible()->name }} hat "{{ $history->fieldName() }}" von
                                                "{{ $history->oldValue() }}" zu "{{ $history->newValue() }}" geändert.
                                                <br/>
                                            @endif
                                        @endforeach
                                @endif
                            </div>
                        @endcan
                    </div>


                    <hr />
                    @can('update', $service)
                    <button id="btnSave" type="submit" class="btn btn-primary">Speichern</button>
                    @else
                        <a class="btn btn-primary" href="{{ route('calendar', $service->day->date->year, $service->day->date->month) }}">Zurück</a>
                    @endcan
                </form>
            </div>
        </div>
        @if (Auth::user()->canEditGeneral)
        <hr />
        <form action="{{ route('services.destroy', $service->id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" type="submit" title="Gottesdiensteintrag (UNWIDERRUFLICH!) löschen"><span class="fa fa-trash"></span> Gottesdiensteintrag löschen</button>
        </form>
        @endif

        <script>
            function setDefaultTime() {
                if ($('select[name=location_id]').val() == '') {
                    $('input[name=time]').attr('placeholder', 'HH:MM');
                    $('#special_location').show();
                    $('#special_location input').first().focus();
                } else {
                    $('input[name=time]').attr('placeholder', 'HH:MM, leer lassen für: ' + ($('select[name=location_id]').children("option:selected").data('time')));
                    $('#special_location_input').val('');
                    $('#special_location').hide();
                }
            }

            $(document).ready(function () {

                $('.peopleSelect').select2({
                    placeholder: 'Eine oder mehrere Personen (keine Anmerkungen!)',
                    allowClear: true,
                    multiple: true,
                    allowclear: true,
                    tags: true,
                    createTag: function (params) {
                        return {
                            id: params.term,
                            text: params.term,
                            newOption: true
                        }
                    },
                    templateResult: function (data) {
                        var $result = $("<span></span>");

                        $result.text(data.text);

                        if (data.newOption) {
                            $result.append(" <em>(Neue Person anlegen)</em>");
                        }

                        return $result;
                    },
                });


                setDefaultTime();

                if ($('select[name=location_id] option').length > 2) {
                    $('select[name=location_id]').focus()
                } else {
                    $('input[name=pastor]').focus();
                }

                $('select[name=location_id]').change(function () {
                    setDefaultTime();
                });

                $('#needPredicant').change(function(){
                    if ($(this).prop( "checked" )) {
                        $('input[name=pastor]').val('').attr('disabled', true);
                    } else {
                        $('input[name=pastor]').attr('disabled', false).focus();
                    }
                });

                $('#btnSave').click(function(event){
                    event.preventDefault();
                    $('#frmEdit input, #frmEdit select, #frmEdit textarea').each(function() {
                        $(this).attr('disabled', false);
                    });
                    $('#frmEdit').submit();
                });

                // Javascript to enable link to tab
                var url = document.location.toString();
                if (url.match('#')) {
                    $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
                }

                // Change hash for page-reload
                $('.nav-tabs a').on('shown.bs.tab', function (e) {
                    window.location.hash = e.target.hash;
                })

            });

        </script>
    @endcomponent
@endsection
