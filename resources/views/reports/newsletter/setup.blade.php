@extends('layouts.app')

@section('title', 'Gottesdienstliste für den Newsletter erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                @select(['name' => 'city', 'label' => 'Newsletter für folgende Kirchengemeinde', 'items' => $cities])
                @input(['name' => 'start', 'label'=> 'Gottesdienste von', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker', 'value' => date('d.m.Y')])
                @input(['name' => 'end', 'label'=> 'Bis', 'placeholder' => 'TT.MM.JJJJ', 'class' => 'datepicker', 'value' => \Carbon\Carbon::now()->addDays(7)->format('d.m.Y')])
                @checkbox(['name' => 'mixOutlook', 'label' => 'Veranstaltungen aus dem Outlook-Kalender mit aufnehmen. ', 'value' => 1, 'checked' => true])
                @checkbox(['name' => 'mixOP', 'label' => 'Veranstaltungen aus dem Online-Planer mit aufnehmen. ', 'value' => 1, 'checked' => true])
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection
