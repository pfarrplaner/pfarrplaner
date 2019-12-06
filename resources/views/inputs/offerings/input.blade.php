@extends('layouts.app')

@section('title') Opferplan für {{ $city->name }}, {{ $year}} @endsection

@section('content')
    <form method="post" action="{{ route('inputs.save', $input->getKey()) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <table class="table table-fluid">
                    <thead>
                    <tr>
                        <th>Gottesdienst</th>
                        <th>Opferzweck</th>
                        <th>Typ</th>
                        <th>Anmerkungen</th>
                        <th>Opferzähler 1</th>
                        <th>Opferzähler 2</th>
                        <th>Betrag</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($services as $service)
                        <tr data-service="{{$service->id}}">
                            <th>
                                {{ $service->day->date->format('d.m.Y') }}
                                , {{ strftime('%H:%M Uhr', strtotime($service->time)) }}<br/>
                                {{ $service->locationText() }}
                            </th>
                            <td>
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                           id="service_{{ $service->id }}_offering_goal"
                                           name="service[{{$service->id}}][offering_goal]"
                                           value="{{ $service->offering_goal }}"/>
                                    @if ($loop->index >0)<a href="#" class="btn btn-sm btn-secondary btnCopyPrevious"
                                                            title="Inhalte der vorherigen Zeile kopieren"><span
                                                class="fa fa-copy"></span> Vorherige übernehmen</a>@endif
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="radio" name="service[{{$service->id}}][offering_type]" value=""
                                               autocomplete="off" @if($service->offering_type == '')checked @endif>
                                        <label class="form-check-label">
                                            Eigener Beschluss
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="service[{{$service->id}}][offering_type]" value="eO"
                                               autocomplete="off" @if($service->offering_type == 'eO')checked @endif>
                                        <label class="form-check-label">
                                            Empfohlenes Opfer
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" name="service[{{$service->id}}][offering_type]" value="PO"
                                               autocomplete="off" @if($service->offering_type == 'PO')checked @endif>
                                        <label class="form-check-label">
                                            Pflichtopfer
                                        </label>
                                    </div>
                                </div>
                            <td>
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                           id="service_{{ $service->id }}_offering_description"
                                           name="service[{{$service->id}}][offering_description]"
                                           value="{{ $service->offering_description }}"/>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                           id="service_{{ $service->id }}_offerings_counter1"
                                           name="service[{{$service->id}}][offerings_counter1]"
                                           value="{{ $service->offerings_counter1 }}"/>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                           id="service_{{ $service->id }}_offerings_counter2"
                                           name="service[{{$service->id}}][offerings_counter2]"
                                           value="{{ $service->offerings_counter2 }}"/>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <input type="text" class="form-control"
                                           id="service_{{ $service->id }}_offering_amount"
                                           name="service[{{$service->id}}][offering_amount]"
                                           value="{{ $service->offering_amount }}"/>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Änderungen speichern</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.btnCopyPrevious').click(function (e) {
                e.preventDefault();
                var thisService = $(this).parent().parent().parent().data('service');
                var prevService = $(this).parent().parent().parent().prev().data('service');

                $('#service_' + thisService + '_offering_goal').val($('#service_' + prevService + '_offering_goal').val());
                $('#service_' + thisService + '_offering_description').val($('#service_' + prevService + '_offering_description').val());
                $('#service_' + thisService + '_offerings_counter1').val($('#service_' + prevService + '_offerings_counter1').val());
                $('#service_' + thisService + '_offerings_counter2').val($('#service_' + prevService + '_offerings_counter2').val());

                $('input:radio[name="service[' + thisService + '][offering_type]"]').val([$('input[name="service[' + prevService + '][offering_type]"]:checked').val()]);
            });
        });
    </script>
@endsection