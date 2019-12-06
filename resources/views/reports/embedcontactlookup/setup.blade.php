@extends('layouts.app')

@section('title', 'HTML-Code für eine Veranstaltungstabelle erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group form-group-hideable for-table-cities for-table-cc  for-table-baptismalservices">
                    <label class="control-label">Widget für folgende Kirchengemeinde erstellen:</label>
                    <select name="city" class="form-control">
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="cors-origin">Aufrufende Website:</label>
                    <input type="text" class="form-control" name="cors-origin" value=""
                           placeholder="z.B. https://www.tailfingen.de/"/>
                </div>
                <hr/>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection
