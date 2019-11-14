@extends('layouts.app')

@section('title', 'Gottesdienst bearbeiten')

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
@endsection

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
                            @peopleselect(['name' => 'participants[P][]', 'label' => 'Pfarrer*in', 'people' => $users, 'value' => $service->pastors, 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')]) @endpeopleselect
                            @checkbox(['name' => 'need_predicant', 'label' => 'Für diesen Gottesdienst wird ein Prädikant benötigt.', 'value' => $service->need_predicant, 'enabled' => Auth::user()->can('gd-pfarrer-bearbeiten')]) @endcheckbox
                            @peopleselect(['name' => 'participants[O][]', 'label' => 'Organist*in', 'people' => $users, 'value' => $service->organists, 'enabled' => Auth::user()->can('gd-organist-bearbeiten')]) @endpeopleselect
                            @peopleselect(['name' => 'participants[M][]', 'label' => 'Mesner*in', 'people' => $users, 'value' => $service->sacristans, 'enabled' => Auth::user()->can('gd-mesner-bearbeiten')]) @endpeopleselect
                            @peopleselect(['name' => 'participants[A][]', 'label' => 'Sonstige Beteiligte', 'people' => $users, 'value' => $service->otherParticipants, 'enabled' => Auth::user()->can('gd-allgemein-bearbeiten')]) @endpeopleselect
                        @endtab
                        @include('partials.service.tabs.special')
                        @include('partials.service.tabs.offerings')
                        @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten', 'gd-kasualien-nur-statistik'])
                            @include('partials.service.tabs.rites')
                        @endcanany
                        @include('partials.service.tabs.cc')
                        @can('admin')
                            @include('partials.service.tabs.history')
                        @endcan
                        @include('partials.service.tabs.comments')
                    @endtabs

                    <hr />
                    <input type="hidden" name="routeBack" value="{{ $backRoute }}" />
                    @can('update', $service)
                    <button type="submit" class="btn btn-primary btnSave" @if($backRoute) data-route="{{ $backRoute }}" @endif>Speichern und schließen</button>
                    <button type="submit" class="btn btn-primary btnSave" data-route="{{ route('services.edit', ['service' => $service->id]) }}">Speichern</button>
                    <a id="btnBack" class="btn btn-warning" @if ($backRoute)href="{{ $backRoute }}" @else href="{{ route('calendar',['year' => $service->day->date->year, 'month' => $service->day->date->month]) }}" @endif>Schließen ohne Änderungen</a>
                    @else
                        <a class="btn btn-primary" @if ($backRoute)href="{{ $backRoute }}" @else href="{{ route('calendar',['year' => $service->day->date->year, 'month' => $service->day->date->month]) }}" @endif>Zurück</a>
                    @endcan
                </form>
            </div>
        </div>
        @can('gd-loeschen')
        <hr />
        <form class="form-inline" style="display: inline;" action="{{ route('services.destroy', $service->id)}}" method="post">
            @csrf
            @method('DELETE')
            <button class="btn btn-danger" type="submit" title="Gottesdiensteintrag (UNWIDERRUFLICH!) löschen"><span class="fa fa-trash"></span> Gottesdiensteintrag löschen</button>
        </form>
        @endcan
        <a class="btn btn-secondary" href="{{ route('services.ical', $service) }}" title="In Outlook übernehmen"><span class="fa fa-calendar-alt"></span> In Outlook übernehmen</a>

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
    @endcomponent
@endsection
