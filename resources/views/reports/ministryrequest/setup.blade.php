@extends('layouts.app')

@section('title', 'Dienstanfrage senden')

@section('content')
    <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'configure']) }}">
        @csrf
        @component('components.ui.card')
            @slot('cardHeader')
                Dienstanfrage erstellen
            @endslot
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Weiter &gt;</button>
            @endslot
            @select(['name' => 'ministry', 'label' => 'Anfrage für folgenden Dienst senden', 'items' => \App\Ministry::all()])
            @input(['name' => 'start', 'label' => 'Gottesdienste von', 'class' => 'datepicker', 'placeholder' => 'TT.MM.JJJJ', 'value' => \Carbon\Carbon::now()->format('d.m.Y')])
            @input(['name' => 'end', 'label' => 'Bis', 'class' => 'datepicker', 'placeholder' => 'TT.MM.JJJJ', 'value' => $maxDate->format('d.m.Y')])
            @locationselect(['name' => 'locations[]', 'label' => 'Auf Gottesdienste an folgendenn Orten beschränken', 'locations' => $locations])
        @endcomponent
    </form>
@endsection
