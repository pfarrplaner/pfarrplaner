@extends('layouts.app')

@section('title') Organistenplan für {{ $city->name }}, {{ $year}} @endsection

@section('content')
    @component('components.container')
        <div class="loader">
            <h1>Das Formular wird geladen... <span class="fa fa-spin fa-spinner"></span></h1>
            Bei {{ $services->count() }} Gottesdiensten könnte das etwas dauern. Bitte habe einen Augenblick Geduld.
        </div>
        <div class="page" style="display: none;">
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
            <div class="card">
                <div class="card-header">
                    {{ $input->title }} für {{ $city->name }} ({{ $year }})
                </div>
                <div class="card-body">
                    @component('components.errors')
                    @endcomponent
                    <form method="post" id="frm" action="{{ route('inputs.save', $input->getKey()) }}">
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
                                        {{ $service->day->date->format('d.m.Y') }}
                                        , {{ strftime('%H:%M Uhr', strtotime($service->time)) }}<br/>
                                        {{ $service->locationText() }}
                                    </th>
                                    <td>
                                        <div class="form-group">
                                            <select class="form-control peopleSelect"
                                                    name="service[{{$service->id}}][participants][O][]" multiple
                                                    @cannot('gd-organist-bearbeiten') disabled
                                                    @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)">
                                                @foreach ($users as $user)
                                                    <option value="{{ $user->id }}"
                                                            @if($service->organists->contains($user)) selected @endif>{{ $user->name }}</option>
                                                @endforeach
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
                        <hr/>
                        <button type="submit" id="submit" class="btn btn-primary">Änderungen speichern</button>
                    </form>
                </div>
            </div>
        </div>
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
                    }
                });

                $('#submit').click(function (e) {
                    $('tr.data-row').show();
                });

                findEmpty();

                $('.peopleSelect').selectize({
                    create: true,
                    render: {
                        option_create: function (data, escape) {
                            return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                        }
                    },
                });

                $( ".loader" ).delay(800).fadeOut(400, function(){
                    $( ".page" ).fadeIn(400);
                });
            });

        </script>
    @endcomponent
@endsection
