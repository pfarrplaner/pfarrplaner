@extends('layouts.app')
@section('title', 'Dienstreiseantrag erstellen')

@section('navbar-left')
    <button class="btn btn-primary" onclick="document.getElementById('setupForm').submit()">Erstellen</button>
@endsection

@section('content')
    @component('components.ui.card')
        @slot('cardHeader')
            Abwesenheitseintrag auswählen
        @endslot
        <form id="setupForm" method="post" action="{{ route('reports.render', $report) }}">
            @csrf
            <div class="form-group">
                <label for="absence">Abwesenheitseintrag</label>
                <select name="absence" class="form-control">
                    @foreach($absences as $absence)
                        <option value="{{ $absence->id }}">
                            {{ $absence->durationText }} {{ $absence->reason }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    @endcomponent
@endsection
