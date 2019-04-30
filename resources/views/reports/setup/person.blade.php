@extends('layouts.app')

@section('title', 'Gottesdienstliste für eine Person erstellen')

@section('content')
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                Gottesdienstliste für eine Person erstellen
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif
                <form method="post" action="{{ route('reports.render', $report) }}">
                    @csrf
                    <div class="form-group">
                        <label for="person"><span class="fa fa-user"></span>&nbsp;Nach dieser Person suchen</label>
                        <select class="form-control fancy-select2" name="person" multiple />
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @if($user->id == Auth::user()->id) selected @endif>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start">Gottesdienste von:</label>
                        <input type="text" class="form-control" name="start" value="{{ date('d.m.Y') }}" placeholder="TT.MM.JJJJ" />
                    </div>
                    <div class="form-group">
                        <label for="end">Bis:</label>
                        <input type="text" class="form-control" name="end" value="{{ $maxDate->date->format('d.m.Y') }}" placeholder="TT.MM.JJJJ" />
                    </div>
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    </div>
@endsection
