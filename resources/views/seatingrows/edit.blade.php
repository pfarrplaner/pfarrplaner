@extends('layouts.app')

@section('title', 'Neue Reihe anlegen')

@section('content')
    @component('components.container')
        <form method="post" action="{{ route('seatingRow.update', ['seatingRow' => $seatingRow->id]) }}">
            @csrf
            @method('PATCH')
            @component('components.ui.card')
                @slot('cardFooter')
                    <button type="submit" class="btn btn-primary">Speichern</button>
                @endslot
                <p><b>{{ $seatingRow->seatingSection->location->name }}</b> / {{ $seatingRow->seatingSection->title }}
                    ({{ $seatingRow->seatingSection->seating_model->getTitle() }})</p>
                @hidden(['name' => 'seating_section_id', 'value' => $seatingRow->seatingSection->id])
                @input(['label' => 'Bezeichnung', 'name' => 'title', 'value' => $seatingRow->title])
                @if(is_a($seatingRow->seatingSection->seating_model, \App\Seating\RowBasedSeatingModel::class))
                    @input(['label' => 'Max. Sitzplätze', 'name' => 'seats', 'value' => $seatingRow->seats])
                    @input(['label' => 'Teilmöglichkeit', 'name' => 'split', 'value' => $seatingRow->split, 'placeholder' => 'Einzelne Teilbereiche, z.B. 2,1,2'])
                @endif
                @if(is_a($seatingRow->seatingSection->seating_model, \App\Seating\CinemaSeatingModel::class))
                    @input(['label' => 'Anzahl der Sitzplätze in der Reihe', 'name' => 'seats', 'value' => $seatingRow->seats])
                    @input(['label' => 'Freizulassende Sitzplätze zwischen Haushalten', 'name' => 'spacing', 'value' => $seatingRow->spacing])
                @endif
            @endcomponent
        </form>
    @endcomponent
@endsection
