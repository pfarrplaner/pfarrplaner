@extends('layouts.app')

@section('title', 'Kirche / Gottesdienstort anlegen')

@section('content')
    <form method="post" action="{{ route('locations.store') }}">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            @endslot
            @input(['name' => 'name', 'label' => 'Name'])
            @select(['name' => 'city_id', 'label' => 'Kirchengemeinde', 'items' => $cities])
            @input(['name' => 'default_time', 'label' => 'Gottesdienst um'])
            @input(['name' => 'cc_default_location', 'label' => 'Wenn parallel Kinderkirche stattfindet, dann normalerweise hier'])
            @select(['name' => 'alternate_location_id', 'label' => 'Wenn parallel Kinderkirche stattfindet, dann normalerweise hier', 'items' => $alternateLocations, 'empty' => true])
            @input(['name' => 'at_text', 'label' => 'Ortsangabe, wenn ein Gottesdienst hier stattfindet', 'placeholder' => 'Ortsangabe, wenn ein Gottesdienst hier stattfindet'])
            @input(['name' => 'general_location_name', 'label' => 'Allgemeine Ortsangabe', 'placeholder' => 'z.B.: in Tailfingen'])
            @textarea(['name' => 'instructions', 'label' => 'Wichtige Informationen für Besucher'])
        @endcomponent
    </form>
@endsection
