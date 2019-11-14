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
                    @csrf
                    @tabheaders
                        @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => true]) @endtabheader
                        @tabheader(['id' => 'special', 'title' => 'Besonderheiten']) @endtabheader
                        @canany(['gd-opfer-lesen','gd-opfer-bearbeiten'])
                            @tabheader(['id' => 'offerings', 'title' => 'Opfer']) @endtabheader
                        @endcanany
                        @canany(['gd-kinderkirche-lesen', 'gd-kinderkirche-bearbeiten'])
                            @tabheader(['id' => 'cc', 'title' => 'Kinderkirche']) @endtabheader
                        @endcanany
                        @endtabheaders

                    @tabs
                        @tab(['id' => 'home', 'active' => true])
                            @hidden(['name' => 'city_id', 'value' => $city->id]) @endhidden
                            @dayselect(['name' => 'day_id', 'label' => 'Datum', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'days' => $days, 'value' => $day]) @enddayselect
                            @locationselect(['name' => 'location_id', 'label' => 'Kirche / Gottesdienstort', 'locations' => $locations, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endlocationselect
                            @input(['name' => 'special_location', 'label' => 'Freie Ortsangabe', 'id' => 'special_location', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endinput
                            @input(['name' => 'time', 'label' => 'Uhrzeit (leer lassen für Standarduhrzeit)', 'placeholder' => 'HH:MM', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endinput
                            @peopleselect(['name' => 'participants[P][]', 'label' => 'Pfarrer*in', 'people' => $users, 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')]) @endpeopleselect
                            @checkbox(['name' => 'need_predicant', 'label' => 'Für diesen Gottesdienst wird ein Prädikant benötigt.', 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')]) @endcheckbox
                            @peopleselect(['name' => 'participants[O][]', 'label' => 'Organist*in', 'people' => $users, 'enabled' => Auth::user()->can('gd-organist-bearbeiten')]) @endpeopleselect
                            @peopleselect(['name' => 'participants[M][]', 'label' => 'Mesner*in', 'people' => $users,  'enabled' => Auth::user()->can('gd-mesner-bearbeiten')]) @endpeopleselect
                            @component('components.service.otherParticipantsWithText', ['users' => $users]) @endcomponent
                            @peopleselect(['name' => 'participants[A][]', 'label' => 'Sonstige Beteiligte', 'people' => $users, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endpeopleselect
                        @endtab
                        @tab(['id' => 'special'])
                            @checkbox(['name' => 'baptism', 'label' => 'Dies ist ein Taufgottesdienst.', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), ]) @endcheckbox
                            @checkbox(['name' => 'eucharist', 'label' => 'Dies ist ein Abendmahlsgottesdienst.', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), ]) @endcheckbox
                            @input(['name' => 'description', 'label' => 'Anmerkungen', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')]) @endinput
                            @textarea(['name' => 'internal_remarks', 'label' => 'Interne Anmerkungen', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')]) @endtextarea
                            @selectize(['name' => 'tags[]', 'label' => 'Kennzeichnungen', 'items' => $tags,  'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endselectize
                            @selectize(['name' => 'serviceGroups[]', 'label' => 'Dieser Gottesdienst gehört zu folgenden Gruppen', 'items' => $serviceGroups, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endselectize
                        @endtab
                        @tab(['id' => 'offerings'])
                            @input(['name' => 'offerings_counter1', 'label' => 'Opferzähler*in 1', 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                            @input(['name' => 'offerings_counter2', 'label' => 'Opferzähler*in 2',  'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                            @input(['name' => 'offerings_goal', 'label' => 'Opferzweck', 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                            @radiogroup(['name' => 'offering_type', 'label' => 'Opfertyp', 'items' => ['eigener Beschluss' => '', 'empfohlenes Opfer' => 'eO', 'Pflichtopfer' => 'PO'], 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endradiogroup
                            @input(['name' => 'offerings_description', 'label' => 'Anmerkungen zum Opfer', 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                            @input(['name' => 'offerings_amount', 'label' => 'Opfersumme', 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                        @endtab
                        @tab(['id' => 'cc'])
                            @checkbox(['name' => 'cc', 'label' => 'Parallel findet Kinderkirche statt.', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')]) @endcheckbox
                            <br />
                            @input(['name' => 'cc_location', 'label' => 'Ort der Kinderkirche', 'placeholder' => 'Leer lassen für ', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')]) @endinput
                            @input(['name' => 'cc_lesson', 'label' => 'Lektion', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')]) @endinput
                            @input(['name' => 'cc_staff', 'label' => 'Mitarbeiter', 'placeholder' => 'Name, Name, ...', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')]) @endinput
                        @endtab
                    @endtabs

                    <hr />
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
