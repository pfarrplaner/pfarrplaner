@extends('layouts.app')

@section('title', 'Kirchengemeinde bearbeiten')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Kirchengemeinde bearbeiten
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('cities.update', $city->id) }}">
                    @method('PATCH')
                    @csrf
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" name="name" value="{{ $city->name }}"/>
                    </div>
                    <div class="form-group">
                        <label for="public_events_calendar_url">URL für einen öffentlichen Kalender auf elkw.de</label>
                        <input type="text" class="form-control" name="public_events_calendar_url" value="{{ $city->public_events_calendar_url }}"/>
                    </div>
                    <hr/>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
