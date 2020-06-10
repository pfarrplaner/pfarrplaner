@extends('layouts.app')

@section('title', 'QR-Codes für Gottesdienste erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf

                @selectize(['label' => 'QR-Codes für folgende Kirchengemeinde drucken', 'name' => 'city', 'items' => $cities])
                <div class="form-group">
                    <label for="start">Gottesdienste von:</label>
                    <input type="text" class="form-control datepicker" name="start" value="{{ date('d.m.Y') }}"
                           placeholder="TT.MM.JJJJ"/>
                </div>
                <div class="form-group">
                    <label for="end">Bis:</label>
                    <input type="text" class="form-control datepicker" name="end" value="{{ $maxDate->date->format('d.m.Y') }}"
                           placeholder="TT.MM.JJJJ"/>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection
