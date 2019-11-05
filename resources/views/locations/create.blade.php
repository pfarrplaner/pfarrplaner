@extends('layouts.app')

@section('title', 'Kirche / Gottesdienstort anlegen')

@section('content')
    @component('components.container')
    <div class="card">
        <div class="card-header">
            Kirche / Gottesdienstort hinzufügen
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
                <div class="form-group">
                    <label for="alternate_location_id">Alternativer Ort, wenn hier kein Gottesdienst stattfindet:</label>
                    <select class="form-control" name="alternate_location_id">
                        <option></option>
                        @foreach($alternateLocations as $alternateLocation)
                            <option value="{{ $alternateLocation->id }}">{{ $alternateLocation->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="at_text">Ortsangabe, wenn ein Gottesdienst hier stattfindet:</label>
                    <input type="text" class="form-control" id="at_text" name="at_text" placeholder="z.B.: in der Peterskirche; auf dem Friedhof; im Gemeindezentrum Stiegel"/>
                </div>
                <div class="form-group">
                    <label for="general_location_name">Allgemeine Ortsangabe:</label>
                    <input type="text" class="form-control" id="general_location_name" name="general_location_name" placeholder="z.B.: in Tailfingen"/>
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
