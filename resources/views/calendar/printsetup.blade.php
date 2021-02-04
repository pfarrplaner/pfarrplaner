@extends('layouts.app')

@section('title', 'Monatsplan erstellen')

@section('content')
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                Monatsplan erstellen
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
                <form method="post" action="{{ route('calendar.print', $year.'-'.$month) }}">
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
                    <div class="form-group"> <!-- Radio group !-->
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="excludeEmptyDays" value="1"
                                   id="excludeEmptyDays" checked>
                            <label class="form-check-label" for="excludeEmptyDays">
                                Tage ohne Gottesdiensteinträge weglassen
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="alwaysIncludeSundays" value="1"
                                   id="alwaysIncludeSundays" checked>
                            <label class="form-check-label" for="alwaysIncludeSundays">
                                Sonntage immer mit einbeziehen (auch wenn keine Gottesdienste eingetragen sind)
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="highlight">Folgenden Namen farblich hervorheben:</label>
                        <input type="text" class="form-control" name="highlight" value="{{ $lastName }}" />
                    </div>
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    </div>
@endsection
