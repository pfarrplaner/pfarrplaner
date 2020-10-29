@extends('layouts.app')

@section('title', 'Neue Reihe anlegen')

@section('content')
    @component('components.container')
        <form method="post" action="{{ route('seatingRow.store') }}">
            @csrf
            @component('components.ui.card')
                @slot('cardFooter')
                    <button type="submit" class="btn btn-primary">Speichern</button>
                @endslot
                <p><b>{{ $seatingSection->location->name }}</b> / {{ $seatingSection->title }}
                    ({{ $seatingSection->seating_model->getTitle() }})</p>
                @hidden(['name' => 'seating_section_id', 'value' => $seatingSection->id])
                @input(['label' => 'Bezeichnung', 'name' => 'title'])
                @if(is_a($seatingSection->seating_model, \App\Seating\RowBasedSeatingModel::class))
                    @input(['label' => 'Max. Haushalte', 'name' => 'divides_into'])
                    @input(['label' => 'Max. Kapazität', 'name' => 'seats'])
                @endif
                @if(is_a($seatingSection->seating_model, \App\Seating\CinemaSeatingModel::class))
                    @input(['label' => 'Anzahl der Sitzplätze in der Reihe', 'name' => 'seats'])
                    @input(['label' => 'Freizulassende Sitzplätze zwischen Haushalten', 'name' => 'spacing'])
                @endif
            @endcomponent
        </form>
    @endcomponent
@endsection
