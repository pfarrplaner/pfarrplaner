@extends('layouts.app')

@section('title', 'Terminliste erstellen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Terminliste erstellen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('reports.render', $report) }}">
                    @csrf
                    <div class="form-group">
                        <label class="control-label">Terminliste für folgende Kirchengemeinde erstellen:</label>
                        <select class="form-control" name="city">
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">
                                    {{$city->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start">Startdatum</label>
                        <input class="form-control datepicker" type="text" name="start" value="{{ \Carbon\Carbon::now()->startOfMonth()->addMonth(1)->format('d.m.Y') }}" placeholder="TT.MM.JJJJ" />
                    </div>
                    <div class="form-group">
                        <label for="end">Enddatum</label>
                        <input class="form-control datepicker" type="text" name="end" value="{{ \Carbon\Carbon::now()->startOfMonth()->addMonth(2)->subDay(1)->format('d.m.Y') }}" placeholder="TT.MM.JJJJ" />
                    </div>
                    @checkbox(['name' => 'mix_outlook', 'label' => 'Veranstaltungen aus dem Outlook-Kalender mit aufnehmen.', 'value' => true]) @endcheckbox
                    @checkbox(['name' => 'mix_op', 'label' => 'Veranstaltungen aus dem Online-Planer mit aufnehmen.', 'value' => false]) @endcheckbox
                    <hr />
                    <button type="submit" class="btn btn-primary">Weiter &gt;</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
