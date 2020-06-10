@extends('layouts.app')

@section('title', 'Gottesdienst hinzufügen')

@section('navbar-left')
@endsection


@section('content')
    <form method="post" action="{{ route('services.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Informationen zum Gottesdienst @endslot
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary">Hinzufügen</button>
                    @endslot
                    @tabheaders
                    @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => true]) @endtabheader
                    @tabheader(['id' => 'special', 'title' => 'Besonderheiten']) @endtabheader
                    @canany(['gd-opfer-lesen','gd-opfer-bearbeiten'])
                        @tabheader(['id' => 'offerings', 'title' => 'Opfer']) @endtabheader
                    @endcanany
                    @canany(['gd-kinderkirche-lesen', 'gd-kinderkirche-bearbeiten'])
                        @tabheader(['id' => 'cc', 'title' => 'Kinderkirche']) @endtabheader
                    @endcanany
                    @tabheader(['id' => 'streaming', 'title' => 'Streaming']) @endtabheader
                    @tabheader(['id' => 'sermon', 'title' => 'Predigt']) @endtabheader
                    @if(\App\Integrations\KonfiApp\KonfiAppIntegration::isActive($city))
                        @tabheader(['id' => 'konfiapp', 'title' => 'Konfi-App']) @endtabheader
                    @endif
                    @endtabheaders

                    @tabs
                    @tab(['id' => 'home', 'active' => true])
                    @hidden(['name' => 'city_id', 'value' => $city->id])
                    @dayselect(['name' => 'day_id', 'label' => 'Datum', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'days' => $days, 'value' => $day])
                    @locationselect(['name' => 'location_id', 'label' => 'Kirche / Gottesdienstort', 'locations' => $locations, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'special_location', 'label' => 'Freie Ortsangabe', 'id' => 'special_location', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'time', 'label' => 'Uhrzeit (leer lassen für Standarduhrzeit)', 'placeholder' => 'HH:MM', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @endtab
                    @tab(['id' => 'special'])
                    @checkbox(['name' => 'baptism', 'label' => 'Dies ist ein Taufgottesdienst.', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), ])
                    @checkbox(['name' => 'eucharist', 'label' => 'Dies ist ein Abendmahlsgottesdienst.', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), ])
                    @input(['name' => 'title', 'label' => 'Abweichender Titel', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')])
                    @input(['name' => 'description', 'label' => 'Anmerkungen', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')])
                    @textarea(['name' => 'internal_remarks', 'label' => 'Interne Anmerkungen', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')])
                    @selectize(['name' => 'tags[]', 'label' => 'Kennzeichnungen', 'items' => $tags,  'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @selectize(['name' => 'serviceGroups[]', 'label' => 'Dieser Gottesdienst gehört zu folgenden Gruppen', 'items' => $serviceGroups, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @endtab
                    @tab(['id' => 'offerings'])
                    @input(['name' => 'offerings_counter1', 'label' => 'Opferzähler*in 1', 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @input(['name' => 'offerings_counter2', 'label' => 'Opferzähler*in 2',  'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @input(['name' => 'offering_goal', 'label' => 'Opferzweck', 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @radiogroup(['name' => 'offering_type', 'label' => 'Opfertyp', 'items' => ['eigener Beschluss' => '', 'empfohlenes Opfer' => 'eO', 'Pflichtopfer' => 'PO'], 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @input(['name' => 'offering_description', 'label' => 'Anmerkungen zum Opfer', 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @input(['name' => 'offering_amount', 'label' => 'Opfersumme', 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @endtab
                    @tab(['id' => 'cc'])
                    @checkbox(['name' => 'cc', 'label' => 'Kinderkirche wird angeboten', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                    <br/>
                    @input(['name' => 'cc_alt_time', 'label' => 'Vom Gottesdienst abweichende Uhrzeit (sonst leer lassen)', 'placeholder' => 'HH:MM', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                    @input(['name' => 'cc_location', 'label' => 'Ort der Kinderkirche', 'placeholder' => 'Leer lassen für ', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                    @input(['name' => 'cc_lesson', 'label' => 'Lektion', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                    @input(['name' => 'cc_staff', 'label' => 'Mitarbeiter', 'placeholder' => 'Name, Name, ...', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                    @endtab
                    @tab(['id' => 'streaming'])
                    @input(['name' => 'youtube_url', 'label' => 'Youtube-URL', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'cc_streaming_url', 'label' => 'URL zu einem parallel gestreamten Kindergottesdienst', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'offerings_url', 'label' => 'URL zu einer Seite für Onlinespenden', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'meeting_url', 'label' => 'URL zu einer Seite für ein "virtuelles Kirchencafé"', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'recording_url', 'label' => 'URL zu einer Audioaufzeichnung des Gottesdiensts', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @upload(['name' => 'songsheet', 'label' => 'Liedblatt zum Gottesdienst', 'accept' => '.pdf'])
                    @endtab
                    @tab(['id' => 'sermon'])
                        @input(['name' => 'sermon_title', 'label' => 'Titel der Predigt', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                        @input(['name' => 'sermon_reference', 'label' => 'Predigttext', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                        @textarea(['name' => 'sermon_description', 'label' => 'Kurzer Anreißer zur Predigt', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                        @upload(['name' => 'sermon_image', 'label' => 'Titelbild zur Predigt', 'accept' => '.jpg,.jpeg'])
                        @input(['name' => 'external_url', 'label' => 'Externe Seite zur Predigt', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @endtab
                    @if(\App\Integrations\KonfiApp\KonfiAppIntegration::isActive($city))
                        @tab(['id' => 'konfiapp'])
                        @selectize(['name' => 'konfiapp_event_type', 'label' => 'Art der Veranstaltung', 'items' => \App\Integrations\KonfiApp\KonfiAppIntegration::get($city)->listEventTypes()->sortBy('name'), 'empty' => true, 'placeholder' => 'Leer = keine Punkte für den Besuch'])
                        @endtab
                    @endif
                    @endtabs
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Beteiligte Personen @endslot
                    @peopleselect(['name' => 'participants[P][]', 'label' => 'Pfarrer*in', 'people' => $users, 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')])
                    @checkbox(['name' => 'need_predicant', 'label' => 'Für diesen Gottesdienst wird ein Prädikant benötigt.', 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')])
                    @peopleselect(['name' => 'participants[O][]', 'label' => 'Organist*in', 'people' => $users, 'enabled' => Auth::user()->can('gd-organist-bearbeiten')])
                    @peopleselect(['name' => 'participants[M][]', 'label' => 'Mesner*in', 'people' => $users,  'enabled' => Auth::user()->can('gd-mesner-bearbeiten')])
                    @component('components.service.otherParticipantsWithText', ['users' => $users, 'ministries' => $ministries]) @endcomponent
                    @peopleselect(['name' => 'participants[A][]', 'label' => 'Sonstige Beteiligte', 'people' => $users, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
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

            $('#needPredicant').change(function () {
                if ($(this).prop("checked")) {
                    $('input[name=pastor]').val('').attr('disabled', true);
                } else {
                    $('input[name=pastor]').attr('disabled', false).focus();
                }
            });

        });

        var ctrMinistryRows = {{ isset($service) ? count($service->ministries()) : 0 }};

        function enableMinistryRows() {
            $('.btnDeleteMinistryRow').click(function(){
                $(this).parent().parent().remove();
            });

            $('.ministryTitleSelect').selectize({
                create: true,
                placeholder: 'Auswählen oder eingeben',
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">' + escape(data.input) + '</div>';
                    }
                },
            });
        }

        $(document).ready(function(){
            enableMinistryRows();

            $('#btnAddMinistryRow').click(function(e){
                e.preventDefault();
                ctrMinistryRows++;
                $('#otherParticipantsWithText').append(
                    '<div class="row form-group ministry-row" style="display:none;" id="ministryRow'+ctrMinistryRows+'">'
                    +'<div class="col-5">'
                    +'<input class="form-control" type="text" name="ministries['+ctrMinistryRows+'][description]" value="" />'
                    +'</div>'
                    +'<div class="col-6">'
                    +'<select type="form-control" name="ministries['+ctrMinistryRows+'][people][]" id="ministrySelect'+ctrMinistryRows+'" multiple placeholder="Eine oder mehrere Personen (keine Anmerkungen!)">'
                    +'</select>'
                    +'</div>'
                    +'<div class="col-1">'
                    +'<button class="btnDeleteMinistryRow btn btn-danger" title="Zeile löschen"><span class="fa fa-trash"></span></button>'
                    +'</div>'
                    +'</div>'
                );
                $('#ministrySelect'+ctrMinistryRows).attr('disabled', $('#peopleTemplate_input').attr('disabled'));
                $('#peopleTemplate_input option').each(function(){
                    $('#ministrySelect'+ctrMinistryRows).append('<option value="'+$(this).attr('value')+'">'+$(this).html()+'</option>');
                });
                $('#ministrySelect'+ctrMinistryRows).selectize({
                    create: true,
                    render: {
                        option_create: function (data, escape) {
                            return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                        }
                    },
                });
                $('#ministryRow'+ctrMinistryRows).show();
                enableMinistryRows();
            });


        });


    </script>
@endsection
