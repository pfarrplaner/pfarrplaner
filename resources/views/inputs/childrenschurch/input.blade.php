@extends('layouts.app')

@section('title', 'Plan für die Kinderkirche in '.$city->name.', '.$year)

@section('content')
    <form method="post" action="{{ route('inputs.save', $input->getKey()) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <table class="table table-fluid">
                    <thead>
                    </thead>
                    <tbody>
                    @foreach($services as $service)
                        <tr>
                            <td colspan="4" class="service-row @if($service->cc) cc @else no-cc @endif">
                                <input type="checkbox" name="service[{{$service->id}}][cc]" class="ccbox"
                                       @if($service->cc) checked @endif  data-service="{{ $service->id }}"/>
                                {{ strftime('%A, %d.%m.%Y', $service->date->getTimestamp()) }}
                                , {{ $service->ccTimeText(false, true) }} ({{ $service->locationText() }})
                            </td>
                        </tr>
                        <tr class="data-row @if($service->cc) cc @else no-cc @endif">
                            <td>
                                <div class="form-group">
                                    <label for="cc_location">Ort der Kinderkirche</label>
                                    <input type="text" id="#service{{$service->id}}-location"
                                           class="form-control location-input"
                                           name="service[{{$service->id}}][cc_location]"
                                           value="{{ $service->cc_location }}"
                                           @if(is_object($service->location))placeholder="Leer lassen für {{ $service->location->cc_default_location }}"@endif/>
                                </div>
                            </td>
                            <td>
                                @input(['name' => 'cc_alt_time', 'label' => 'Vom Gottesdienst abweichende Uhrzeit (sonst leer lassen)', 'placeholder' => 'HH:MM', 'value' => $service->ccTimeText(true, false, ':', false, false, true), 'enabled' => Auth::user()->can('gd-kinderkirche-bearbeiten')])
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="cc_lesson">Lektion</label>
                                    <input type="text" class="form-control" name="service[{{$service->id}}][cc_lesson]"
                                           value="{{ $service->cc_lesson }}"/>
                                </div>
                            </td>
                            <td>
                                <div class="form-group">
                                    <label for="cc_staff">Mitarbeiter</label>
                                    <input type="text" class="form-control" name="service[{{$service->id}}][cc_staff]"
                                           value="{{ $service->cc_staff }}" placeholder="Name, Name, ..."/>
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
            $('.ccbox').change(function () {
                if ($(this).prop('checked')) {
                    $(this).parent().removeClass('no-cc').addClass('cc');
                    $(this).parent().parent().next().removeClass('no-cc').addClass('cc');
                    setTimeout(function (el) {
                        $(el).parent().parent().next().children('input').first().focus();
                    }, 500, this);
                } else {
                    $(this).parent().removeClass('cc').addClass('no-cc');
                    $(this).parent().parent().next().removeClass('cc').addClass('no-cc')
                }
            });
        });
    </script>
@endsection
