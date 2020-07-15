@extends('layouts.app')

@section('title', 'Gottesdienst bearbeiten')

@section('navbar-left')
    <li class="nav-item">
        <a id="btnBack" class="btn btn-navbar" @if ($backRoute)href="{{ $backRoute }}"
           @else href="{{ route('calendar',['year' => $service->day->date->year, 'month' => $service->day->date->month]) }}"
           @endif title="Schließen ohne Änderungen">
            <span class="fa fa-times"></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('services.ical', $service) }}" title="In Outlook übernehmen"><span
                class="fa fa-calendar-alt"></span> In Outlook übernehmen</a>
    </li>
    @can('gd-loeschen')
        <li class="nav-item">
            <form class="form-inline" style="display: inline;" action="{{ route('services.destroy', $service->id)}}"
                  method="post">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit" title="Gottesdiensteintrag (UNWIDERRUFLICH!) löschen"><span
                        class="fa fa-trash"></span></button>
            </form>
        </li>
    @endcan
    @if($service->city->youtube_channel_url)
        @if($service->youtube_url)
            <li class="nav-item">
                <a id="btn_link_video" class="nav-link" href="{{ $service->youtube_url }}" target="_blank"><span class="fab fa-youtube"></span> Video</a>
            </li>
            <li class="nav-item">
                <a id="btn_link_livedash" class="nav-link" href="{{ \App\Helpers\YoutubeHelper::getLiveDashboardUrl($service->city, $service->youtube_url) }}" target="_blank"><span class="fa fa-video"></span> Live-Dashboard</a>
            </li>
        @else
            <li class="nav-item" id="new_livestream">
                <a id="btn_build_livestream" class="nav-link" href="#">Neuen Livestream erstellen</a>
            </li>
            <li class="nav-item" id="link_livedash" style="display:none;">
                <a id="btn_link_livedash" class="nav-link" href="{{ \App\Helpers\YoutubeHelper::getLiveDashboardUrl($service->city, $service->youtube_url) }}" target="_blank"><span class="fa fa-video"></span> Live-Dashboard</a>
            </li>
        @endif
    @endif
@endsection

@section('content')
    <form id="frmEdit" method="post" action="{{ route('services.update', $service->id) }}"
          enctype="multipart/form-data">
        @method('PATCH')
        @csrf
        <div class="row">
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Informationen zum Gottesdienst @endslot
                    @slot('cardFooter')
                        @can('update', $service)
                            <button type="submit" class="btn btn-primary btnSave"
                                    @if($backRoute) data-route="{{ $backRoute }}" @endif>Speichern und schließen
                            </button>
                            <button type="submit" class="btn btn-primary btnSave"
                                    data-route="{{ route('services.edit', ['service' => $service->id]) }}">Speichern
                            </button>
                        @else
                            <a class="btn btn-primary" @if ($backRoute)href="{{ $backRoute }}"
                               @else href="{{ route('calendar',['year' => $service->day->date->year, 'month' => $service->day->date->month]) }}" @endif>Zurück</a>
                        @endcan
                    @endslot

                    @tabheaders
                    @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => ($tab=='home' || $tab == '')]) @endtabheader
                    @tabheader(['id' => 'special', 'title' => 'Besonderheiten', 'active' => ($tab=='special')]) @endtabheader
                    @canany(['gd-opfer-lesen','gd-opfer-bearbeiten'])
                        @tabheader(['id' => 'offerings', 'title' => 'Opfer', 'active' => ($tab=='offerings')]) @endtabheader
                    @endcanany
                    @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten', 'gd-kasualien-nur-statistik'])
                        @tabheader(['id' => 'rites', 'title' => 'Kasualien', 'active' => ($tab=='rites')]) @endtabheader
                    @endcan
                    @canany(['gd-kinderkirche-lesen', 'gd-kinderkirche-bearbeiten'])
                        @tabheader(['id' => 'cc', 'title' => 'Kinderkirche', 'active' => ($tab=='cc')]) @endtabheader
                    @endcanany
                    @tabheader(['id' => 'streaming', 'title' => 'Streaming', 'active' => ($tab=='streaming')]) @endtabheader
                    @tabheader(['id' => 'sermon', 'title' => 'Predigt', 'active' => ($tab=='sermon')]) @endtabheader
                    @if(\App\Integrations\KonfiApp\KonfiAppIntegration::isActive($service->city))
                        @tabheader(['id' => 'konfiapp', 'title' => 'KonfiApp', 'active' => ($tab=='konfiapp')]) @endtabheader
                    @endif
                    @tabheader(['id' => 'comments', 'title' => 'Kommentare', 'active' => ($tab=='comments'), 'count' => (count($service->comments ?? []))]) @endtabheader
                    @can('admin')
                        @tabheader(['id' => 'history', 'title' => 'Bearbeitungen', 'active' => ($tab=='history')]) @endtabheader
                    @endcan('admin')
                    @endtabheaders

                    @tabs
                    @tab(['id' => 'home', 'active' => ($tab=='home' || $tab == '')])
                    @hidden(['name' => 'city_id', 'value' => $service->city_id])
                    @dayselect(['name' => 'day_id', 'label' => 'Datum', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'days' => $days, 'value' => $service->day])
                    @locationselect(['name' => 'location_id', 'label' => 'Kirche / Gottesdienstort', 'locations' => $locations, 'value' => $service->location, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'special_location', 'label' => 'Freie Ortsangabe', 'id' => 'special_location', 'value' => $service->special_location, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'time', 'label' => 'Uhrzeit (leer lassen für Standarduhrzeit)', 'placeholder' => 'HH:MM', 'value' => $service->timeText(false, ':', false, false, true), 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @endtab
                    @tab(['id' => 'special', 'active' => ($tab=='special')])
                    @checkbox(['name' => 'baptism', 'label' => 'Dies ist ein Taufgottesdienst.', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'value' => $service->baptism])
                    @checkbox(['name' => 'eucharist', 'label' => 'Dies ist ein Abendmahlsgottesdienst.', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'value' => $service->eucharist])
                    @input(['name' => 'title', 'label' => 'Abweichender Titel', 'value' => $service->title, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')])
                    @input(['name' => 'description', 'label' => 'Anmerkungen', 'value' => $service->description, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')])
                    @textarea(['name' => 'internal_remarks', 'label' => 'Interne Anmerkungen', 'value' => $service->internal_remarks, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')])
                    @selectize(['name' => 'tags[]', 'label' => 'Kennzeichnungen', 'items' => $tags, 'value' => $service->tags, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @selectize(['name' => 'serviceGroups[]', 'label' => 'Dieser Gottesdienst gehört zu folgenden Gruppen', 'items' => $serviceGroups, 'value' => $service->serviceGroups, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @endtab
                    @tab(['id' => 'offerings', 'active' => ($tab=='offerings')])
                    @input(['name' => 'offerings_counter1', 'label' => 'Opferzähler*in 1', 'value' => $service->offerings_counter1, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @input(['name' => 'offerings_counter2', 'label' => 'Opferzähler*in 2', 'value' => $service->offerings_counter2, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @input(['name' => 'offering_goal', 'label' => 'Opferzweck', 'value' => $service->offering_goal, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @radiogroup(['name' => 'offering_type', 'label' => 'Opfertyp', 'items' => ['eigener Beschluss' => '', 'empfohlenes Opfer' => 'eO', 'Pflichtopfer' => 'PO'], 'value' => $service->offering_type, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @input(['name' => 'offering_description', 'label' => 'Anmerkungen zum Opfer', 'value' => $service->offering_description, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @input(['name' => 'offering_amount', 'label' => 'Opfersumme', 'value' => $service->offering_amount, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')])
                    @endtab
                    @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten', 'gd-kasualien-nur-statistik'])
                        @include('partials.service.tabs.rites')
                    @endcanany
                    @tab(['id' => 'cc', 'active' => ($tab=='cc')])
                    @checkbox(['name' => 'cc', 'label' => 'Parallel findet Kinderkirche statt.', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten'), 'value' => $service->cc])
                    <br/>
                    @input(['name' => 'cc_alt_time', 'label' => 'Vom Gottesdienst abweichende Uhrzeit (sonst leer lassen)', 'placeholder' => 'HH:MM', 'value' => $service->ccTimeText(true, false, ':', false, false, true), 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                    @input(['name' => 'cc_location', 'label' => 'Ort der Kinderkirche', 'placeholder' => 'Leer lassen für ', 'value' => $service->cc_location, 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                    @input(['name' => 'cc_lesson', 'label' => 'Lektion', 'value' => $service->cc_lesson, 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                    @input(['name' => 'cc_staff', 'label' => 'Mitarbeiter', 'placeholder' => 'Name, Name, ...', 'value' => $service->cc_staff, 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                    @endtab
                    @tab(['id' => 'streaming', 'active' => ($tab=='streaming')])
                    @if($service->youtube_url)
                        <small>Diesem Gottesdienst ist bereits ein Livestream zugeordnet.</small>
                    @else
                        <small id="build_livestream">Diesem Gottesdienst noch kein Livestream zugeordnet.</small>
                    @endif
                    @input(['name' => 'youtube_url', 'label' => 'Youtube-URL', 'value' => $service->youtube_url, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'cc_streaming_url', 'label' => 'URL zu einem parallel gestreamten Kindergottesdienst', 'value' => $service->cc_streaming_url, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'offerings_url', 'label' => 'URL zu einer Seite für Onlinespenden', 'value' => $service->offerings_url, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'meeting_url', 'label' => 'URL zu einer Seite für ein "virtuelles Kirchencafé"', 'value' => $service->meeting_url, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'recording_url', 'label' => 'URL zu einer Audioaufzeichnung des Gottesdiensts', 'value' => $service->recording_url, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @upload(['name' => 'songsheet', 'label' => 'Liedblatt zum Gottesdienst', 'value' => $service->songsheet, 'prettyName' => $service->day->date->format('Ymd').'-Liedblatt', 'accept' => '.pdf'])
                    @endtab
                    @tab(['id' => 'sermon', 'active' => ($tab=='sermon')])
                    @input(['name' => 'sermon_title', 'label' => 'Titel der Predigt', 'value' => $service->sermon_title, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @input(['name' => 'sermon_reference', 'label' => 'Predigttext', 'value' => $service->sermon_reference, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @textarea(['name' => 'sermon_description', 'label' => 'Kurzer Anreißer zur Predigt', 'value' => $service->sermon_description, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @upload(['name' => 'sermon_image', 'label' => 'Titelbild zur Predigt', 'value' => $service->sermon_image, 'prettyName' => $service->day->date->format('Ymd').'-Predigtbild', 'accept' => '.jpg,.jpeg'])
                    @input(['name' => 'external_url', 'label' => 'Externe Seite zur Predigt', 'value' => $service->external_url, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                    @endtab
                    @if(\App\Integrations\KonfiApp\KonfiAppIntegration::isActive($service->city))
                        @tab(['id' => 'konfiapp', 'active' => ($tab=='konfiapp')])
                            @selectize(['name' => 'konfiapp_event_type', 'label' => 'Art der Veranstaltung', 'items' => \App\Integrations\KonfiApp\KonfiAppIntegration::get($service->city)->listEventTypes()->sortBy('name'), 'empty' => true, 'placeholder' => 'Leer = keine Punkte für den Besuch', 'value' => $service->konfiapp_event_type])
                            @if($service->konfiapp_event_qr)
                                <div>
                                    Diesem Gottesdienst ist ein QR-Code mit der ID <code>{{ $service->konfiapp_event_qr }}</code> zugeordnet. <br />
                                    <a class="btn btn-secondary" href="{{ route('report.step', ['report' => 'KonfiAppQR', 'step' => 'single', 'service' => $service->id]) }}">QR-Code drucken</a>
                                </div>
                            @endif
                        @endtab
                    @endif
                    @can('admin')
                        @include('partials.service.tabs.history')
                    @endcan
                    @include('partials.service.tabs.comments')
                    @endtabs

                    <input type="hidden" name="routeBack" value="{{ $backRoute }}"/>
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Beteiligte Personen @endslot
                    @peopleselect(['name' => 'participants[P][]', 'label' => 'Pfarrer*in', 'people' => $users, 'value' => $service->pastors, 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')])
                    @checkbox(['name' => 'need_predicant', 'label' => 'Für diesen Gottesdienst wird ein Prädikant benötigt.', 'value' => $service->need_predicant, 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')])
                    @peopleselect(['name' => 'participants[O][]', 'label' => 'Organist*in', 'people' => $users, 'value' => $service->organists, 'enabled' => Auth::user()->can('gd-organist-bearbeiten')])
                    @peopleselect(['name' => 'participants[M][]', 'label' => 'Mesner*in', 'people' => $users, 'value' => $service->sacristans, 'enabled' => Auth::user()->can('gd-mesner-bearbeiten')])
                    @component('components.service.otherParticipantsWithText', ['users' => $users, 'service' => $service, 'ministries' => $ministries]) @endcomponent
                    @peopleselect(['name' => 'participants[A][]', 'label' => 'Sonstige Beteiligte', 'people' => $users, 'value' => $service->otherParticipants, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')])
                @endcomponent
                @include('components.attachments', ['object' => $service])
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>var attachments = {{ count($service->attachments) }};</script>
    <script src="{{ asset('js/pfarrplaner/attachments.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <script>
        var commentRoute = '{{ route('comments.store') }}';
        var commentOwner = '{{ $service->id }}';
        var commentOwnerClass = 'App\\Service';
    </script>
    <script src="{{ asset('js/pfarrplaner/comments.js') }}"></script>
    <script>
        function setDefaultTime() {
            if ($('select[name=location_id]  option:selected').val() == 0) {
                $('input[name=time]').attr('placeholder', 'HH:MM');
                $('#special_location').show();
                $('#special_location input').first().focus();
            } else {
                $('input[name=time]').attr('placeholder', 'HH:MM, leer lassen für: ' + ($('select[name=location_id]').children("option:selected").data('time')));
                $('#special_location_input').val('');
                $('#special_location').hide();
            }
        }

        var originalFormData;
        var dirty = false;

        function isDirty() {
            return (originalFormData !== $('form#frmEdit').serialize()) || dirty;
        }

        function setDirty(status = true) {
            dirty = status;
            dirtHandler();
        }

        function dirtHandler() {
            if (isDirty()) {
                $('.btnSave').show();
                $('#btnBack').hide();
                $('.btn-rite').addClass('disabled');
                $('#ritesAlert').show();
            } else {
                $('#btnBack').show();
                $('.btnSave').hide();
                $('.btn-rite').removeClass('disabled');
                $('#ritesAlert').hide();
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

            $('.btnSave').click(function (event) {
                event.preventDefault();
                var route = $(this).data('route');
                if (route != '') $('input[name=routeBack]').val(route);
                $('#frmEdit input, #frmEdit select, #frmEdit textarea').each(function () {
                    $(this).attr('disabled', false);
                });
                $('#frmEdit').submit();
            });

            // save original form data for dirt-checking
            originalFormData = $('form#frmEdit').serialize();

            // dirty handler:
            /*
            $(":input:not(button):not([type=hidden])").change(dirtHandler);
            $(":input:not(button):not([type=hidden])").click(dirtHandler);
            $(":input:not(button):not([type=hidden])").on('keyup', dirtHandler);
            */


        });

        var ctrMinistryRows = {{ isset($service) ? count($service->ministries()) : 0 }};

        function enableMinistryRows() {
            $('.btnDeleteMinistryRow').click(function () {
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

        $(document).ready(function () {
            enableMinistryRows();

            $('#btnAddMinistryRow').click(function (e) {
                e.preventDefault();
                ctrMinistryRows++;
                $('#otherParticipantsWithText').append(
                    '<div class="row form-group ministry-row" style="display:none;" id="ministryRow' + ctrMinistryRows + '">'
                    + '<div class="col-5">'
                    + '<input class="form-control" type="text" name="ministries[' + ctrMinistryRows + '][description]" value="" />'
                    + '</div>'
                    + '<div class="col-6">'
                    + '<select type="form-control" name="ministries[' + ctrMinistryRows + '][people][]" id="ministrySelect' + ctrMinistryRows + '" multiple placeholder="Eine oder mehrere Personen (keine Anmerkungen!)">'
                    + '</select>'
                    + '</div>'
                    + '<div class="col-1">'
                    + '<button class="btnDeleteMinistryRow btn btn-danger" title="Zeile löschen"><span class="fa fa-trash"></span></button>'
                    + '</div>'
                    + '</div>'
                );
                $('#ministrySelect' + ctrMinistryRows).attr('disabled', $('#peopleTemplate_input').attr('disabled'));
                $('#peopleTemplate_input option').each(function () {
                    $('#ministrySelect' + ctrMinistryRows).append('<option value="' + $(this).attr('value') + '">' + $(this).html() + '</option>');
                });
                $('#ministrySelect' + ctrMinistryRows).selectize({
                    create: true,
                    render: {
                        option_create: function (data, escape) {
                            return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                        }
                    },
                });
                $('#ministryRow' + ctrMinistryRows).show();
                enableMinistryRows();
            });


            $('#btn_build_livestream').click(function (e) {
                e.preventDefault();
                $('#build_livestream').html('<img src="{{ asset('img/spinner.gif') }}" /> Livestream wird erstellt...');
                fetch('{{ route('broadcast.create', $service) }}').then((response) => {
                    return response.json();
                }).then((data) => {
                    $('#link_livedash a').attr('href', data.liveDashboard);
                    $('#link_livedash').show();
                    $('#btn_build_livestream').html('<span class="fab fa-youtube"></span> Video');
                    $('#btn_build_livestream').attr('id', 'btn_link_video');
                    $('#btn_link_video').attr('href', data.url);
                    $('input[name=youtube_url]').val(data.url);
                    $('#build_livestream').html('<small>Diesem Gottesdienst ist bereits ein Livestream zugeordnet.</small>');
                });
            });


        });


    </script>
@endsection
