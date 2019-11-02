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

                    <ul class="nav nav-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link @if($tab == 'home') active @endif" href="#home" role="tab" data-toggle="tab">Allgemeines</a>
                        </li>
                        <li class="nav-item  ">
                            <a class="nav-link @if($tab == 'special') active @endif" href="#special" role="tab" data-toggle="tab">Besonderheiten</a>
                        </li>
                        @canany(['gd-opfer-lesen','gd-opfer-bearbeiten'])
                        <li class="nav-item ">
                            <a class="nav-link @if($tab == 'offerings') active @endif" href="#offerings" role="tab" data-toggle="tab">Opfer</a>
                        </li>
                        @endcanany
                        @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten', 'gd-kasualien-nur-statistik'])
                        <li class="nav-item ">
                            <a class="nav-link @if($tab == 'rites') active @endif" href="#rites" role="tab" data-toggle="tab">Kasualien</a>
                        </li>
                        @endcan
                        @canany(['gd-kinderkirche-lesen', 'gd-kinderkirche-bearbeiten'])
                        <li class="nav-item ">
                            <a class="nav-link @if($tab == 'cc') active @endif" href="#cc" role="tab" data-toggle="tab">Kinderkirche</a>
                        </li>
                        @endcanany
                        <li class="nav-item">
                            <a class="nav-link @if($tab == 'comments') active @endif" href="#comments" role="tab" data-toggle="tab">Kommentare <span class="badge badge-primary" id="commentCount">{{ $service->commentsForCurrentUser->count() }}</span></a>
                        </li>
                        @can('admin')
                        <li class="nav-item">
                            <a class="nav-link @if($tab == 'history') active @endif" href="#history" role="tab" data-toggle="tab">Bearbeitungen</a>
                        </li>
                        @endcan('admin')
                    </ul>

                    <div class="tab-content">
                        <br />
                        @include('partials.service.tabs.home')
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
                    </div>

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
