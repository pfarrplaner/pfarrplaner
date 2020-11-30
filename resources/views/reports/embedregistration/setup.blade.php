@extends('layouts.app')

@section('title', 'HTML-Code für eine Veranstaltungstabelle erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Folgende Kirchengemeinden mit einbeziehen:</label>
                    @foreach ($cities as $city)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="includeCities[]" value="{{ $city->id }}"
                                   id="defaultCheck{{$city->id}}" checked>
                            <label class="form-check-label" for="defaultCheck{{$city->id}}">
                                {{$city->name}}
                            </label>
                        </div>
                    @endforeach
                </div>
                @input(['label' => 'Begrenzen auf einen Tag?', 'name' => 'singleDay', 'placeholder' => 'Leer lassen, um alle Gottesdienste zu zeigen', 'class' => 'datepicker'])
                <div class="form-group">
                    <label for="cors-origin">Aufrufende Website:</label>
                    <input type="text" class="form-control" name="cors-origin" value=""
                           placeholder="z.B. https://www.tailfingen-evangelisch.de/"/>
                </div>
                <hr/>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection
