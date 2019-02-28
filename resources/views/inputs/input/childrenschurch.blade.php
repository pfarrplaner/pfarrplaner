@extends('layouts.app')

@section('title', 'Plan für die Kinderkirche ({{ $city->name }}, {{ $year}})')

@section('content')
    <div class="py-5">
        <div class="card">
            <div class="card-header">
                {{ $input->title }} für {{ $city->name }} ({{ $year }})
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif
                <form method="post" action="{{ route('inputs.save', $input->getKey()) }}">
                    @csrf
                    <table class="table table-fluid">
                        <thead>
                        </thead>
                        <tbody>
                        @foreach($services as $service)
                            <tr>
                                <td colspan="3" class="service-row @if($service->cc) cc @else no-cc @endif">
                                    <input type="checkbox" name="service[{{$service->id}}][cc]" class="ccbox" @if($service->cc) checked @endif  data-service="{{ $service->id }}"/>
                                    {{ strftime('%A, %d.%m.%Y', $service->day->date->getTimestamp()) }}, {{ strftime('%H:%M Uhr', strtotime($service->time)) }} ({{ $service->locationText() }})
                                </td>
                            </tr>
                            <tr class="data-row @if($service->cc) cc @else no-cc @endif">
                                <td>
                                    <div class="form-group">
                                        <label for="cc_location">Ort der Kinderkirche</label>
                                        <input type="text" id="#service{{$service->id}}-location" class="form-control location-input" name="service[{{$service->id}}][cc_location]" value="{{ $service->cc_location }}" @if($service->special_location=='')placeholder="Leer lassen für {{ $service->location->cc_default_location }}"@endif/>
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="cc_lesson">Lektion</label>
                                        <input type="text" class="form-control" name="service[{{$service->id}}][cc_lesson]" value="{{ $service->cc_lesson }}" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <label for="cc_staff">Mitarbeiter</label>
                                        <input type="text" class="form-control" name="service[{{$service->id}}][cc_staff]" value="{{ $service->cc_staff }}" placeholder="Name, Name, ..."/>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <hr />
                    <button type="submit" class="btn btn-primary">Bearbeiten</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('.ccbox').change(function(){
                if ($(this).prop('checked')) {
                    $(this).parent().removeClass('no-cc').addClass('cc');
                    $(this).parent().parent().next().removeClass('no-cc').addClass('cc');
                    setTimeout(function(el){
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
