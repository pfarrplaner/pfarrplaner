@extends('layouts.app')

@section('title', 'Kirche / Gottesdienstort bearbeiten')

@section('content')
    @component('components.container')
    <form method="post" action="{{ route('locations.update', $location->id) }}">
        @method('PATCH')
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Speichern</button>
            @endslot
            @input(['name' => 'name', 'label' => 'Name', 'value' => $location->name])
            @select(['name' => 'city_id', 'label' => 'Kirchengemeinde', 'items' => $cities, 'value' => $location->city_id])
            @input(['name' => 'default_time', 'label' => 'Gottesdienst um', 'value' => substr($location->default_time, 0, 5)])
            @input(['name' => 'cc_default_location', 'label' => 'Wenn parallel Kinderkirche stattfindet, dann normalerweise hier', 'value' => $location->cc_default_location])
            @select(['name' => 'alternate_location_id', 'label' => 'Wenn parallel Kinderkirche stattfindet, dann normalerweise hier', 'items' => $alternateLocations, 'value' => $location->alternate_location_id, 'empty' => true])
            @input(['name' => 'at_text', 'label' => 'Ortsangabe, wenn ein Gottesdienst hier stattfindet', 'placeholder' => 'Ortsangabe, wenn ein Gottesdienst hier stattfindet', 'value' => $location->at_text])
            @input(['name' => 'general_location_name', 'label' => 'Allgemeine Ortsangabe', 'placeholder' => 'z.B.: in Tailfingen', 'value' => $location->general_location_name])
        @endcomponent
    </form>
    @endcomponent
@endsection
