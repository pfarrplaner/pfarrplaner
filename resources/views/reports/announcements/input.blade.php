@extends('layouts.app')

@section('title', 'Bekanntgaben erstellen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Bekanntgaben erstellen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('reports.render', $report) }}">
                    @csrf
                    <input type="hidden" name="service" value="{{ $service->id }}" />
                    <div class="form-group">
                        <label class="control-label" for="lastService">Herzlichen Dank für das Opfer der Gottesdienste vom...</label>
                        <input class="form-control datepicker" type="text" name="lastService" value="{{ $lastDayWithServices->date->format('d.m.Y') }}" placeholder="TT.MM.JJJJ" />
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="offerings">...in Höhe von ... €.</label>
                        <input class="form-control" type="text" name="offerings" value="" placeholder="123,45" />
                    </div>
                    <div class="form-group">
                        <label class="control-label" for="offering_text">Text zum Opfer</label>
                        <textarea class="form-control" type="text" name="offering_text" placeholder="Wenn vorhanden, z.B. Anschreiben des Landesbischofs"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
