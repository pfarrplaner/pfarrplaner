@extends('layouts.app')

@section('title', 'Kirche anlegen')

@section('content')
    @component('components.container')
    <div class="card">
        <div class="card-header">
            Kirche hinzufügen
        </div>
        <div class="card-body">
            @component('components.errors')
            @endcomponent
            <form method="post" action="{{ route('locations.store') }}">
                <div class="form-group">
                    @csrf
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name"/>
                </div>
                <div class="form-group">
                    <label for="location_id">Kirchengemeinde:</label>
                    <select class="form-control" name="city_id">
                        @foreach ($cities->all() as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="default_time">Gottesdienst um:</label>
                    <input type="text" class="form-control" id="fldDefaultTime" name="default_time"/>
                </div>
                <div class="form-group">
                    <label for="cc_default_location">Wenn parallel Kinderkirche stattfindet, dann normalerweise hier:</label>
                    <input type="text" class="form-control" id="cc_default_location" name="cc_default_location"/>
                </div>
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function($){
            $('#fldSection').focus();
        });
    </script>
    @endcomponent
@endsection
