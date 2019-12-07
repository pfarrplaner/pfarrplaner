@extends('layouts.app')

@section('title', 'Gottesdienst bearbeiten')

@section('navbar-left')
    <li class="nav-item">
        <a id="btnBack" class="btn btn-navbar" @if ($backRoute)href="{{ $backRoute }}" @else href="{{ route('calendar',['year' => $service->day->date->year, 'month' => $service->day->date->month]) }}" @endif title="Schließen ohne Änderungen">
            <span class="fa fa-times"></span>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="{{ route('services.ical', $service) }}" title="In Outlook übernehmen"><span class="fa fa-calendar-alt"></span> In Outlook übernehmen</a>
    </li>
    @can('gd-loeschen')
        <li class="nav-item">
            <form class="form-inline" style="display: inline;" action="{{ route('services.destroy', $service->id)}}" method="post">
                @csrf
                @method('DELETE')
                <button class="btn btn-danger" type="submit" title="Gottesdiensteintrag (UNWIDERRUFLICH!) löschen"><span class="fa fa-trash"></span></button>
            </form>
        </li>
    @endcan
@endsection

@section('content')
    <form id="frmEdit" method="post" action="{{ route('services.update', $service->id) }}">
        @method('PATCH')
        @csrf
        <div class="row">
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Informationen zum Gottesdienst @endslot
                    @slot('cardFooter')
                        @can('update', $service)
                            <button type="submit" class="btn btn-primary btnSave" @if($backRoute) data-route="{{ $backRoute }}" @endif>Speichern und schließen</button>
                            <button type="submit" class="btn btn-primary btnSave" data-route="{{ route('services.edit', ['service' => $service->id]) }}">Speichern</button>
                        @else
                            <a class="btn btn-primary" @if ($backRoute)href="{{ $backRoute }}" @else href="{{ route('calendar',['year' => $service->day->date->year, 'month' => $service->day->date->month]) }}" @endif>Zurück</a>
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
                    @tabheader(['id' => 'comments', 'title' => 'Kommentare', 'active' => ($tab=='comments')]) @endtabheader
                    @can('admin')
                        @tabheader(['id' => 'history', 'title' => 'Bearbeitungen', 'active' => ($tab=='history')]) @endtabheader
                    @endcan('admin')
                    @endtabheaders

                    @tabs
                    @tab(['id' => 'home', 'active' => ($tab=='home' || $tab == '')])
                        @hidden(['name' => 'city_id', 'value' => $service->city_id]) @endhidden
                        @dayselect(['name' => 'day_id', 'label' => 'Datum', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'days' => $days, 'value' => $service->day]) @enddayselect
                        @locationselect(['name' => 'location_id', 'label' => 'Kirche / Gottesdienstort', 'locations' => $locations, 'value' => $service->location, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endlocationselect
                        @input(['name' => 'special_location', 'label' => 'Freie Ortsangabe', 'id' => 'special_location', 'value' => $service->special_location, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endinput
                        @input(['name' => 'time', 'label' => 'Uhrzeit (leer lassen für Standarduhrzeit)', 'placeholder' => 'HH:MM', 'value' => $service->timeText(false, ':', false, false, true), 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endinput
                    @endtab
                    @tab(['id' => 'special', 'active' => ($tab=='special')])
                    @checkbox(['name' => 'baptism', 'label' => 'Dies ist ein Taufgottesdienst.', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'value' => $service->baptism]) @endcheckbox
                    @checkbox(['name' => 'eucharist', 'label' => 'Dies ist ein Abendmahlsgottesdienst.', 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten'), 'value' => $service->eucharist]) @endcheckbox
                    @input(['name' => 'description', 'label' => 'Anmerkungen', 'value' => $service->description, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')]) @endinput
                    @textarea(['name' => 'internal_remarks', 'label' => 'Interne Anmerkungen', 'value' => $service->internal_remarks, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten') || Auth::user()->can('gd-anmerkungen-bearbeiten')]) @endtextarea
                    @selectize(['name' => 'tags[]', 'label' => 'Kennzeichnungen', 'items' => $tags, 'value' => $service->tags, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endselectize
                    @selectize(['name' => 'serviceGroups[]', 'label' => 'Dieser Gottesdienst gehört zu folgenden Gruppen', 'items' => $serviceGroups, 'value' => $service->serviceGroups, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endselectize
                    @endtab
                    @tab(['id' => 'offerings', 'active' => ($tab=='offerings')])
                    @input(['name' => 'offerings_counter1', 'label' => 'Opferzähler*in 1', 'value' => $service->offerings_counter1, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                    @input(['name' => 'offerings_counter2', 'label' => 'Opferzähler*in 2', 'value' => $service->offerings_counter2, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                    @input(['name' => 'offering_goal', 'label' => 'Opferzweck', 'value' => $service->offering_goal, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                    @radiogroup(['name' => 'offering_type', 'label' => 'Opfertyp', 'items' => ['eigener Beschluss' => '', 'empfohlenes Opfer' => 'eO', 'Pflichtopfer' => 'PO'], 'value' => $service->offering_type, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endradiogroup
                    @input(['name' => 'offering_description', 'label' => 'Anmerkungen zum Opfer', 'value' => $service->offering_description, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                    @input(['name' => 'offering_amount', 'label' => 'Opfersumme', 'value' => $service->offering_amount, 'enabled' => Auth::user()->can('gd-opfer-bearbeiten')]) @endinput
                    @endtab
                    @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten', 'gd-kasualien-nur-statistik'])
                        @include('partials.service.tabs.rites')
                    @endcanany
                    @tab(['id' => 'cc', 'active' => ($tab=='cc')])
                    @checkbox(['name' => 'cc', 'label' => 'Parallel findet Kinderkirche statt.', 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten'), 'value' => $service->cc]) @endcheckbox
                    <br />
                    @input(['name' => 'cc_alt_time', 'label' => 'Vom Gottesdienst abweichende Uhrzeit (sonst leer lassen)', 'placeholder' => 'HH:MM', 'value' => $service->ccTimeText(true, false, ':', false, false, true), 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')]) @endinput
                    @input(['name' => 'cc_location', 'label' => 'Ort der Kinderkirche', 'placeholder' => 'Leer lassen für ', 'value' => $service->cc_location, 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')]) @endinput
                    @input(['name' => 'cc_lesson', 'label' => 'Lektion', 'value' => $service->cc_lesson, 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')]) @endinput
                    @input(['name' => 'cc_staff', 'label' => 'Mitarbeiter', 'placeholder' => 'Name, Name, ...', 'value' => $service->cc_staff, 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')]) @endinput
                    @endtab
                    @can('admin')
                        @include('partials.service.tabs.history')
                    @endcan
                    @include('partials.service.tabs.comments')
                    @endtabs

                    <input type="hidden" name="routeBack" value="{{ $backRoute }}" />
                @endcomponent
            </div>
            <div class="col-md-6">
                @component('components.ui.card')
                    @slot('cardHeader')Beteiligte Personen @endslot
                    @peopleselect(['name' => 'participants[P][]', 'label' => 'Pfarrer*in', 'people' => $users, 'value' => $service->pastors, 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')]) @endpeopleselect
                    @checkbox(['name' => 'need_predicant', 'label' => 'Für diesen Gottesdienst wird ein Prädikant benötigt.', 'value' => $service->need_predicant, 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')]) @endcheckbox
                    @peopleselect(['name' => 'participants[O][]', 'label' => 'Organist*in', 'people' => $users, 'value' => $service->organists, 'enabled' => Auth::user()->can('gd-organist-bearbeiten')]) @endpeopleselect
                    @peopleselect(['name' => 'participants[M][]', 'label' => 'Mesner*in', 'people' => $users, 'value' => $service->sacristans, 'enabled' => Auth::user()->can('gd-mesner-bearbeiten')]) @endpeopleselect
                    @component('components.service.otherParticipantsWithText', ['users' => $users, 'service' => $service, 'ministries' => $ministries]) @endcomponent
                    @peopleselect(['name' => 'participants[A][]', 'label' => 'Sonstige Beteiligte', 'people' => $users, 'value' => $service->otherParticipants, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endpeopleselect
                @endcomponent
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
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

            $('#needPredicant').change(function(){
                if ($(this).prop( "checked" )) {
                    $('input[name=pastor]').val('').attr('disabled', true);
                } else {
                    $('input[name=pastor]').attr('disabled', false).focus();
                }
            });

            $('.btnSave').click(function(event){
                event.preventDefault();
                var route = $(this).data('route');
                if (route != '') $('input[name=routeBack]').val(route);
                $('#frmEdit input, #frmEdit select, #frmEdit textarea').each(function() {
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

    </script>
@endsection
