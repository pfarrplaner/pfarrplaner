@extends('layouts.app')

@section('title') Opferplan für {{ $city->name }}, {{ $year}} @endsection

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
                        <tr>
                            <th>Gottesdienst</th>
                            <th>Opferzweck</th>
                            <th>Typ</th>
                            <th>Anmerkungen</th>
                            <th>Opferzähler 1</th>
                            <th>Opferzähler 2</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($services as $service)
                            <tr>
                                <th>
                                    {{ $service->day->date->format('d.m.Y') }}, {{ strftime('%H:%M Uhr', strtotime($service->time)) }}<br />
                                    {{ $service->locationText() }}
                                </th>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="service[{{$service->id}}][offering_goal]" value="{{ $service->offering_goal }}" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <div class="form-check">
                                            <input type="radio" name="service[{{$service->id}}][offering_type]" value="" autocomplete="off" @if($service->offering_type == '')checked @endif>
                                            <label class="form-check-label">
                                                Eigener Beschluss
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="service[{{$service->id}}][offering_type]" value="eO" autocomplete="off" @if($service->offering_type == 'eO')checked @endif>
                                            <label class="form-check-label">
                                                Empfohlenes Opfer
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input type="radio" name="service[{{$service->id}}][offering_type]" value="PO" autocomplete="off" @if($service->offering_type == 'PO')checked @endif>
                                            <label class="form-check-label">
                                                Pflichtopfer
                                            </label>
                                        </div>
                                    </div>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="service[{{$service->id}}][offering_description]" value="{{ $service->offering_description }}" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="service[{{$service->id}}][offerings_counter1]" value="{{ $service->offerings_counter1 }}" />
                                    </div>
                                </td>
                                <td>
                                    <div class="form-group">
                                        <input type="text" class="form-control" name="service[{{$service->id}}][offerings_counter2]" value="{{ $service->offerings_counter2 }}" />
                                    </div>
                                </td>

                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    <hr />
                    <button type="submit" class="btn btn-primary">Änderungen speichern</button>
                </form>
            </div>
        </div>
    </div>
@endsection
