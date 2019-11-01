@extends('layouts.app')

@section('title', 'Kirchengemeinde hinzufügen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Kirchengemeinde hinzufügen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('cities.store') }}">
                    <div class="form-group">
                        @csrf
                        <label for="name">Ort</label>
                        <input type="text" class="form-control" name="name"/>
                    </div>
                    <div class="form-group">
                        <label for="public_events_calendar_url">URL für einen öffentlichen Kalender auf elkw.de</label>
                        <input type="text" class="form-control" name="public_events_calendar_url"/>
                    </div>
                    <hr/>
                    <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
