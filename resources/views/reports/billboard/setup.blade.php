@extends('layouts.app')

@section('title', 'Kirchzettel erstellen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Kirchzettel erstellen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('reports.render', $report) }}">
                    @csrf
                    <div class="form-group">
                        <label class="control-label">Kirchzettel für folgende Kirchengemeinde erstellen:</label>
                        <select class="form-control" name="city">
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">
                                    {{$city->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start">Startdatum</label>
                        <input class="form-control datepicker" type="text" name="start" value="{{ (new \Carbon\Carbon('next Sunday'))->format('d.m.Y') }}" placeholder="TT.MM.JJJJ" />
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-primary">Weiter &gt;</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
