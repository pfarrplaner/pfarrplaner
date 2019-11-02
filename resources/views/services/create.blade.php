@extends('layouts.app')

@section('title', 'Gottesdienst hinzufügen')


@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Gottesdienst hinzufügen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('services.store') }}">
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
                        @canany(['gd-kinderkirche-lesen', 'gd-kinderkirche-bearbeiten'])
                        <li class="nav-item">
                            <a class="nav-link" href="#cc" role="tab" data-toggle="tab">Kinderkirche</a>
                        </li>
                        @endcanany
                    </ul>


                    <div class="tab-content">
                        <br />
                        <div role="tabpanel" class="tab-pane fade in active show" id="home">
                            <div class="form-group">
                                @csrf
                                <label for="day_id">Datum</label>
                                <select class="form-control" name="day_id" @cannot('gd-allgemein-bearbeiten') disabled @endcannot >
                                    @foreach($days as $thisDay)
                                        <option value="{{$thisDay->id}}"
                                                @if (($day) && ($day->id == $thisDay->id)) selected @endif
                                        >{{$thisDay->date->format('d.m.Y')}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="location_id">Kirche</label>
                                <select class="form-control" name="location_id" @cannot('gd-allgemein-bearbeiten') disabled @endcannot>
                                    @foreach($locations as $thisLocation)
                                        <option data-time="{{ strftime('%H:%M', strtotime($thisLocation->default_time)) }}"
                                                data-cc="{{ $thisLocation->cc_default_location }}"
                                                value="{{$thisLocation->id}}">{{$thisLocation->name}}</option>
                                    @endforeach
                                    <option value="">Freie Ortsangabe</option>
                                </select>
                            </div>
                            <div id="special_location" class="form-group">
                                <label for="special_location">Freie Ortsangabe</label>
                                <input id="special_location_input" class="form-control" type="text" name="special_location" @cannot('gd-allgemein-bearbeiten') disabled @endcannot/>
                                <input type="hidden" name="city_id" value="{{ $city->id }}"/>
                            </div>
                            <div class="form-group">
                                <label for="time">Uhrzeit (leer lassen für Standarduhrzeit)</label>
                                <input class="form-control" type="text" name="time" placeholder="HH:MM" @cannot('gd-allgemein-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="participants[P][]"><span class="fa fa-user"></span>&nbsp;Pfarrer*in</label>
                                <select class="form-control peopleSelect" name="participants[P][]" multiple @cannot('gd-pfarrer-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="need_predicant" value="1"
                                       id="needPredicant" @cannot('gd-pfarrer-bearbeiten') disabled @endcannot >
                                <label class="form-check-label" for="needPredicant">
                                    Für diesen Gottesdienst wird ein Prädikant benötigt.
                                </label>
                                <br />
                            </div>
                            <div class="form-group">
                                <label for="participants[O][]"><span class="fa fa-user"></span>&nbsp;Organist*in</label>
                                <select class="form-control peopleSelect" name="participants[O][]" multiple @cannot('gd-organist-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" >{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="participants[M][]"><span class="fa fa-user"></span>&nbsp;Mesner*in</label>
                                <select class="form-control peopleSelect" name="participants[M][]" multiple @cannot('gd-mesner-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" >{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="participants[A][]"><span class="fa fa-users"></span>&nbsp;Weitere Beteiligte</label>
                                <select class="form-control peopleSelect" name="participants[A][]" multiple @cannot('gd-allgemein-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)">
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" >{{ $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane fade" id="special">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="baptism" value="1"
                                       id="baptism" @cannot('gd-taufe-bearbeiten') disabled @endcannot >
                                <label class="form-check-label" for="baptism">
                                    Taufe(n)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="eucharist" value="1"
                                       id="eucharist" @cannot('gd-abendmahl-bearbeiten') disabled @endcannot >
                                <label class="form-check-label" for="eucharist">
                                    Abendmahl
                                </label>
                            </div>
                            <br />
                            <div class="form-group">
                                <label for="description">Anmerkungen</label>
                                <input type="text" class="form-control" name="description" @canany(['gd-allgemein-beabeiten', 'gd-anmerkungen-bearbeiten']) @else disabled @endcanany />
                            </div>
                            <div class="form-group">
                                <label for="internal_remarks">Interne Anmerkungen</label>
                                <textarea rows="5" class="form-control" name="internal_remarks"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="tags">Kennzeichnungen</label>
                                <select class="form-control fancy-selectize" name="tags[]" multiple>
                                    <option></option>
                                    @foreach ($tags as $tag)
                                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="tags">Dieser Gottesdienst gehört zu folgenden Gruppen</label>
                                <select class="form-control" name="serviceGroups[]" multiple id="selectServiceGroups">
                                    <option></option>
                                    @foreach ($serviceGroups as $serviceGroup)
                                        <option value="{{ $serviceGroup->id }}">{{ $serviceGroup->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <script>
                                $(document).ready(function(){
                                    $('#selectServiceGroups').selectize({
                                        create: true,
                                        render: {
                                            option_create: function (data, escape) {
                                                return '<div class="create">Neue Gruppe anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                                            }
                                        },
                                    });
                                });
                            </script>
                        </div>
                        @canany(['gd-opfer-lesen','gd-opfer-bearbeiten'])
                        <div role="tabpanel" class="tab-pane fade" id="offerings">
                            <div class="form-group">
                                <label for="offerings_counter1">Opferzähler*in 1</label>
                                <input class="form-control" type="text" name="offerings_counter1" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="offerings_counter2">Opferzähler*in 2</label>
                                <input class="form-control" type="text" name="offerings_counter2" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="offering_goal">Opferzweck</label>
                                <input class="form-control" type="text" name="offering_goal" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label style="display:block;">Opfertyp</label>
                                <div class="form-check-inline">
                                    <input type="radio" name="offering_type" value="" autocomplete="off" checked @cannot('gd-opfer-bearbeiten') disabled @endcannot >
                                    <label class="form-check-label">
                                        Eigener Beschluss
                                    </label>
                                </div>
                                <div class="form-check-inline">
                                    <input type="radio" name="offering_type" value="eO" autocomplete="off" @cannot('gd-opfer-bearbeiten') disabled @endcannot >
                                    <label class="form-check-label">
                                        Empfohlenes Opfer
                                    </label>
                                </div>
                                <div class="form-check-inline disabled">
                                    <input type="radio" name="offering_type" value="PO" autocomplete="off" @cannot('gd-opfer-bearbeiten') disabled @endcannot >
                                    <label class="form-check-label">
                                        Pflichtopfer
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="offering_description">Anmerkungen zum Opfer</label>
                                <input class="form-control" type="text" name="offering_description" @cannot('gd-opfer-bearbeiten') disabled @endcannot />
                            </div>
                        </div>
                        @endcanany
                        @canany(['gd-kinderkirche-lesen', 'gd-kinderkirche-bearbeiten'])
                        <div role="tabpanel" class="tab-pane fade" id="cc">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="cc" value="1"
                                       id="cc-check" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot >
                                <label class="form-check-label" for="cc">
                                    Parallel findet Kinderkirche statt
                                </label>
                            </div>
                            <br />
                            <div class="form-group">
                                <label for="cc_location">Ort der Kinderkirche:</label>
                                <input class="form-control" type="text" name="cc_location" placeholder="Leer lassen für " @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="cc_lesson">Lektion:</label>
                                <input class="form-control" type="text" name="cc_lesson" @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
                            </div>
                            <div class="form-group">
                                <label for="cc_staff">Mitarbeiter:</label>
                                <input class="form-control" type="text" name="cc_staff" placeholder="Name, Name, ..." @cannot('gd-kinderkirche-bearbeiten') disabled @endcannot />
                            </div>
                        </div>
                        @endcanany
                    </div>
                    <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </form>
            </div>
        </div>
        </div>
        <script>
            function setDefaultTime() {
                if ($('select[name=location_id]').val() == '') {
                    $('input[name=time]').attr('placeholder', 'HH:MM');
                    $('input[name=cc_default_location]').attr('placeholder', '');
                    $('#special_location').show();
                    $('#special_location input').first().focus();
                } else {
                    $('input[name=time]').attr('placeholder', 'HH:MM, leer lassen für: ' + ($('select[name=location_id]').children("option:selected").data('time')));
                    $('input[name=cc_location]').attr('placeholder', 'Leer lassen für ' + ($('select[name=location_id]').children("option:selected").data('cc')));
                    $('#special_location_input').val('');
                    $('#special_location').hide();
                }
            }

            $(document).ready(function () {
                $('.peopleSelect').selectize({
                    create: true,
                    render: {
                        option_create: function (data, escape) {
                            return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                        }
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

            });

        </script>
    @endcomponent
@endsection
