@extends('layouts.app')

@section('title') Gottesdienstplanung für {{ $city->name }}, {{ $year}} @endsection

@section('content')
    <div class="loader">
        <h1>Das Formular wird geladen... <span class="fa fa-spin fa-spinner"></span></h1>
        Bei {{ count($services) }} Gottesdiensten im Jahr {{ $year }} kann das etwas dauern.
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
                            <th>Besonderheiten</th>
                            @foreach($ministries as $ministry)
                                <th>{{ $ministry }}</th>
                            @endforeach
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($services as $service)
                                <tr class="data-row" data-location="{{ $service->locationText() }}">
                                <th>
                                    {{ $service->day->date->format('d.m.Y') }}
                                    , {{ strftime('%H:%M Uhr', strtotime($service->time)) }}<br/>
                                    {{ $service->locationText() }}
                                </th>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control"
                                               name="service[{{$service->id}}][description]"
                                               value="{{ $service->description }}"/>
                                    </div>
                                </td>
                                @foreach($ministries as $key => $ministry)
                                    <td>
                                        <div class="form-group">
                                            @peopleselect(['name' => 'service['.$service->id.'][ministries]['.$key.'][]', 'label' => '', 'people' => $users, 'value' => $service->participantsByCategory($key), 'useItemId' => true])
                                        </div>

                                    </td>
                                @endforeach
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
        $(document).ready(function () {
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

            $('.peopleSelect').selectize({
                create: true,
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    }
                },
            });

            $(".loader").delay(800).fadeOut(200, function () {
                $("#page").fadeIn(200);
            });


        });

    </script>
@endsection
