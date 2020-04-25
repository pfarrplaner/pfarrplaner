@extends('layouts.app')

@section('title', 'Programm für die Kinderkirche erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                @selectize(['name' => 'city', 'label' => 'Kirchengemeinde', 'items' => $cities])
                <div class="form-group">
                    <label for="cors-origin">Aufrufende Website:</label>
                    <input type="text" class="form-control" name="cors-origin" value=""
                           placeholder="z.B. https://www.tailfingen.de/"/>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection

