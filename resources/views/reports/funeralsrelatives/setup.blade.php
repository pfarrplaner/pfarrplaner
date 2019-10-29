@extends('layouts.app')

@section('title', 'Adressliste der Angehörigen erstellen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Adressliste der Angehörigen erstellen
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
                <form method="post" action="{{ route('reports.render', $report) }}">
                    @csrf
                    <div class="form-group"> <!-- Radio group !-->
                        <label class="control-label">Adressliste für Beerdigungen in folgender Kirchengemeinde erstellen:</label>
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
                        <input class="form-control datepicker" type="text" name="start" placeholder="TT.MM.JJJJ" value="{{ \Carbon\Carbon::now()->formatLocalized('01.01.%Y') }}" />
                    </div>
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
    @endcomponent
@endsection
