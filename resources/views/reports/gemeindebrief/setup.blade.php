@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                Gottesdienstliste für den Gemeindebrief erstellen
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
                <form method="post" action="{{ route('reports.gemeindebrief') }}">
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
                    <div class="form-group">
                        <label for="start">Gottesdienste von:</label>
                        <input type="text" class="form-control" name="start" value="{{ date('d.m.Y') }}" placeholder="TT.MM.JJJJ" />
                    </div>
                    <div class="form-group">
                        <label for="end">Bis:</label>
                        <input type="text" class="form-control" name="end" value="{{ $maxDate->date->format('d.m.Y') }}" placeholder="TT.MM.JJJJ" />
                    </div>
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    </div>
@endsection
