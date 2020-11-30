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
                        @input(['label' => 'Max. Sitzplätze', 'name' => 'seats', 'value' => old('seats')])
                        @input(['label' => 'Teilmöglichkeit', 'name' => 'split', 'value' => old('split'), 'placeholder' => 'Einzelne Teilbereiche, z.B. 2,1,2'])
                @endif
                @if(is_a($seatingSection->seating_model, \App\Seating\CinemaSeatingModel::class))
                    @input(['label' => 'Anzahl der Sitzplätze in der Reihe', 'name' => 'seats'])
                    @input(['label' => 'Freizulassende Sitzplätze zwischen Haushalten', 'name' => 'spacing'])
                @endif
                @input(['label' => 'CSS-Farbklasse', 'name' => 'color', 'value' => old('color'), 'placeholder' => '#RRGGBB oder CSS-Farbname'])
            @endcomponent
        </form>
    @endcomponent
@endsection
