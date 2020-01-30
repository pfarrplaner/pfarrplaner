@extends('layouts.app')

@section('title', 'Übersicht der eingenommenen Opfer erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Adressliste für Beerdigungen in folgender Kirchengemeinde
                        erstellen:</label>
                    <select class="form-control fancy-selectize" name="cities[]" multiple>
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">
                                {{$city->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="start">Von:</label>
                    <input class="form-control datepicker" type="text" name="start" placeholder="TT.MM.JJJJ"
                           value="{{ \Carbon\Carbon::now()->formatLocalized('01.01.%Y') }}"/>
                </div>
                <div class="form-group">
                    <label for="end">Bis:</label>
                    <input class="form-control datepicker" type="text" name="end" placeholder="TT.MM.JJJJ"
                           value="{{ \Carbon\Carbon::now()->formatLocalized('01.01.%Y') }}"/>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection
