@extends('layouts.app')

@section('title', 'HTML-Code für eine Veranstaltungstabelle erstellen')

@section('content')
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                HTML-Code für eine Veranstaltungstabelle erstellen
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
                    <div class="form-group form-group-hideable for-table-cities for-table-cc  for-table-baptismalservices">
                        <label class="control-label">Widget für folgende Kirchengemeinde erstellen:</label>
                        <select name="city" class="form-control">
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                        @component('components.validation', ['name' => 'city']) @endcomponent
                    </div>
                    <div class="form-group">
                        <label for="cors-origin">Aufrufende Website:</label>
                        <input type="text" class="form-control" name="cors-origin" value="" placeholder="z.B. https://www.tailfingen.de/" />
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    </div>
@endsection
