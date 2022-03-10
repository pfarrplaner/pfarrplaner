@extends('layouts.app')

@section('title') Organistenplan für {{ $city->name }}, {{ $year}} @endsection

@section('content')
    <div class="loader">
        <h1>Das Formular wird geladen... <span class="fa fa-spin fa-spinner"></span></h1>
    </div>
    <div id="page" style="display:none;">
        <div class="card bg-light">
            <div class="card-header">Filter</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="#filter">Nur folgenden Veranstaltungsort anzeigen:</label>
                    <select class="form-control" id="filter">
                        <option value="" selected>--</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->name }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <br/>
        <form method="post" id="frm" action="{{ route('inputs.save', $input->getKey()) }}">
            <div class="card">
                <div class="card-header">
                    {{ $input->title }} für {{ $city->name }} ({{ $year }})
                </div>
                <div class="card-body">
                    @csrf
                    <table class="table table-fluid">
                        <thead>
                        <tr>
                            <th>Gottesdienst</th>
                            <th>Organist*in</th>
                            <th>Besonderheiten</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($services as $service)
                            <tr class="data-row" data-location="{{ $service->locationText() }}">
                                <th>
                                    {{ $service->date->format('d.m.Y') }}
                                    , {{ strftime('%H:%M Uhr', strtotime($service->time)) }}<br/>
                                    {{ $service->locationText() }}
                                </th>
                                <td>
                                    <div class="form-group">
                                        <select class="form-control peopleSelect"
                                                name="service[{{$service->id}}][participants][O][]" multiple
                                                data-initial="{{ $service->organists->pluck('id')->implode(',') }}"
                                                @cannot('gd-organist-bearbeiten') disabled
                                                @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)">
                                        </select>
                                    </div>

                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="service[{{$service->id}}][description]"
                                               value="{{ $service->description }}"/>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <button type="submit" id="submit" class="btn btn-primary">Änderungen speichern</button>
                </div>
            </div>
        </form>
        <select id="people" style="display: none;">
            @foreach($users as $user)
                <option value="{{ $user->id }}" data-id="{{ $user->id }}">{{ $user->fullName() }}</option>
            @endforeach
        </select>
    </div>
@endsection

@section('scripts')
    <script>
        function findEmpty() {
            $('.organist').each(function () {
                if ($(this).val() == '') {
                    $(this).attr('style', 'background-color: lightred');
                }
            });
        }

        $(document).ready(function () {

            $('.organist').on('change', function () {
                findEmpty();
            });

            $('#filter').change(function () {
                if ($(this).val() != '') {
                    //alert($(this).val());
                    $('tr.data-row').show();
                    $('tr.data-row:not([data-location=\'' + $(this).val() + '\'])').hide();
                } else {
                    $('tr.data-row').show();
                }
            });

            $('#submit').click(function (e) {
                $('tr.data-row').show();
            });

            findEmpty();

            $('.peopleSelect').each(function () {
                var select = this;
                $(this).html($('#people').html());
                var x = $(this).data('initial');
                if (x != '') {
                    $.each(String(x).split(','), function (i, e) {
                        $(select).find('option[value="' + e + '"]').prop('selected', true);
                    });
                }
            });

            $('.peopleSelect').selectize({
                create: true,
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    }
                },
            });

            $(".loader").delay(800).fadeOut(400, function () {
                $("#page").fadeIn(400);
            });


        });

    </script>
@endsection
