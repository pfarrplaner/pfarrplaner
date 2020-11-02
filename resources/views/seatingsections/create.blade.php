@extends('layouts.app')

@section('title', 'Neue Zone anlegen')

@section('content')
    @component('components.container')
        <form method="post" action="{{ route('seatingSection.store') }}">
            @csrf
            @component('components.ui.card')
                @slot('cardFooter')
                    <button type="submit" class="btn btn-primary">Speichern</button>
                @endslot
                @hidden(['name' => 'location_id', 'value' => $location->id])
                @input(['label' => 'Bezeichnung', 'name' => 'title'])
                @input(['label' => 'Priorität', 'name' => 'priority', 'value' => old('priority'), 'type' => 'number'])
                @select(['label' => 'Sitzplatzverteilung', 'name' => 'seating_model', 'items' => \App\Seating\SeatingModels::select()])
            @endcomponent
        </form>
    @endcomponent
@endsection
