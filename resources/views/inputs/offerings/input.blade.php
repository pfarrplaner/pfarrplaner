@extends('layouts.app')

@section('title') Opferplan für {{ $cities->pluck('name')->join(', ') }} ({{ $year}}) @endsection

@section('content')
    <div class="loader">
        <h1><span id="loader_header">Das Formular wird geladen...</span> <span class="fa fa-spin fa-spinner"></span></h1>
        <span id="loader_text">Bei {{ count($services) }} Gottesdiensten im Jahr {{ $year }} kann das etwas dauern.</span>
    </div>
    <div id="page" style="display:none;">
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
                            <tr data-service="{{$service->id}}" data-city="{{ $service->city_id }}"
                                data-day="{{ $service->day_id }}">
                                <th>
                                    {{ $service->day->date->format('d.m.Y') }}
                                    , {{ strftime('%H:%M Uhr', strtotime($service->time)) }}<br/>
                                    {{ $service->locationText() }}
                                </th>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control tracked"
                                               id="service_{{ $service->id }}_offering_goal"
                                               name="service[{{$service->id}}][offering_goal]"
                                               value="{{ $service->offering_goal }}"/>
                                        @if ($loop->index >0)<a href="#"
                                                                class="btn btn-sm btn-secondary btnCopyPrevious"
                                                                title="Inhalte der vorherigen Zeile kopieren"><span
                                                class="fa fa-copy"></span> Vorherige übernehmen</a>@endif
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="radio" name="service[{{$service->id}}][offering_type]" value=""
                                                   autocomplete="off" @if($service->offering_type == '')checked
                                                   @endif class="tracked">
                                            <label class="form-check-label">
                                                Eigener Beschluss
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="service[{{$service->id}}][offering_type]"
                                                   value="eO"
                                                   autocomplete="off" @if($service->offering_type == 'eO')checked
                                                   @endif class="tracked">
                                            <label class="form-check-label">
                                                Empfohlenes Opfer
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="service[{{$service->id}}][offering_type]"
                                                   value="PO"
                                                   autocomplete="off" @if($service->offering_type == 'PO')checked
                                                   @endif class="tracked">
                                            <label class="form-check-label">
                                                Pflichtopfer
                                            </label>
                                        </div>
                                    </div>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control tracked"
                                               id="service_{{ $service->id }}_offering_description"
                                               name="service[{{$service->id}}][offering_description]"
                                               value="{{ $service->offering_description }}"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control tracked"
                                               id="service_{{ $service->id }}_offerings_counter1"
                                               name="service[{{$service->id}}][offerings_counter1]"
                                               value="{{ $service->offerings_counter1 }}"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control tracked"
                                               id="service_{{ $service->id }}_offerings_counter2"
                                               name="service[{{$service->id}}][offerings_counter2]"
                                               value="{{ $service->offerings_counter2 }}"/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control tracked"
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
                    <button class="btn btn-primary btnSubmit">Änderungen speichern</button>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js"></script>
    <script>
        $(document).ready(function () {

            function hasChanges(input) {
                if ((input.type == "text" || input.type == "textarea" || input.type == "hidden") && input.defaultValue != input.value) {
                    return true;
                } else {
                    if ((input.type == "radio" || input.type == "checkbox") && input.defaultChecked != input.checked) {
                        return true
                    } else {
                        if ((input.type == "select-one" || input.type == "select-multiple")) {
                            for (var x = 0; x < input.length; x++) {
                                if (input.options[x].selected != input.options[x].defaultSelected) {
                                    return true;
                                }
                            }
                        }
                    }
                }
                return false;
            }

            function trackRow(input) {
                if (hasChanges(input)) $(input).closest('tr').addClass('changed');
            }

            $('.tracked').change(function () {
                trackRow(this);
            });
            $('.tracked').keydown(function () {
                trackRow(this);
            });

            $('.btnSubmit').click(function (e) {
                e.preventDefault();
                $('tr:not(.changed)').remove();

                var saveCtr = $('tr.changed').length;
                $('#loader_header').html('Änderungen werden gespeichert...');
                $('#loader_text').html('Noch '+saveCtr+' Datensätze müssen gespeichert werden.');

                $("#page").fadeOut(200, function () {
                    $(".loader").fadeIn(200);
                });


                $('tr.changed').each(function () {
                    var service = $(this).data('service');
                    var data = {
                        service_id: service,
                        city_id: $(this).data('city'),
                        day_id: $(this).data('day'),
                        offering_description: $('#service_' + service + '_offering_description').val(),
                        offering_amount: $('#service_' + service + '_offering_amount').val(),
                        offerings_counter1: $('#service_' + service + '_offerings_counter1').val(),
                        offerings_counter2: $('#service_' + service + '_offerings_counter2').val(),
                        offering_type: $('input[name="service[' + service + '][offering_type]"]:checked').val(),
                        _method: 'patch',
                    }
                    axios.post('/services/' + service, data).then((response) => {
                        saveCtr--;
                        if (saveCtr == 0) window.location.href='{{ route('calendar') }}';
                        $('#loader_text').html('Noch '+saveCtr+' Datensätze müssen gespeichert werden.');
                        Promise.resolve(response);
                    });
                });
            });

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

        $(".loader").delay(800).fadeOut(200, function () {
            $("#page").fadeIn(200);
        });


    </script>
@endsection
