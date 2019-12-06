@extends('layouts.app')

@section('title', 'Adressliste der Angehörigen erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Adressliste für Beerdigungen in folgender Kirchengemeinde
                        erstellen:</label>
                    <select class="form-control" name="city">
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">
                                {{$city->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Startdatum:</label>
                    <input class="form-control datepicker" type="text" name="start" placeholder="TT.MM.JJJJ"
                           value="{{ \Carbon\Carbon::now()->formatLocalized('01.01.%Y') }}"/>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection
