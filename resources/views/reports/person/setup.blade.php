@extends('layouts.app')

@section('title', 'Gottesdienstliste für eine Person erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group">
                    <label for="person"><span class="fa fa-user"></span>&nbsp;Nach dieser Person suchen</label>
                    <select class="form-control fancy-selectize" name="person"/>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}"
                                @if($user->id == Auth::user()->id) selected @endif>{{ $user->name }}</option>
                        @endforeach
                        </select>
                </div>
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
