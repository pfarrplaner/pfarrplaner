@extends('layouts.app')

@section('title', 'Gottesdienstliste für den Gemeindebrief erstellen  (Schritt 2)')

@section('content')
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                Gottesdienstliste für den Gemeindebrief erstellen (Schritt 2)
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
                <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'render']) }}">
                    @csrf
                    <input type="hidden" name="start" value="{{ $start }}" />
                    <input type="hidden" name="end" value="{{ $end }}" />
                    @foreach ($includeCities as $city)
                        <input type="hidden" name="includeCities[]" value="{{ $city }}" />
                    @endforeach
                    <div class="row">
                        @for($i=0; $i<4; $i++)
                        <div class="col-6">
                            <div class="form-group">
                                <label for="locations[{{ $i }}]">Spalte {{ $i+1 }}</label>
                                <select class="form-control fancy-selectize" name="locations[{{ $i }}]">
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" @if($loop->index==$i) selected @endif>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                            @if ($i==1)</div><div class="row">@endif
                        @endfor
                    </div>
                    <div class="form-group">
                        <label for="pageno">Seitennummer (erste Seite)</label>
                        <input class="form-control" type="text" name="pageno" value="14" />
                    </div>

                    <hr />
                    <h2>Termine ohne Gottesdienst</h2>
                    @foreach($empty as $occasion)
                        <div class="form-group">
                            <label for="empty[{{$occasion['location']->id}}][{{$occasion['day']->id}}]">{{ $occasion['day']->date->formatLocalized('%A, %d. %B') }}, {{ $occasion['location']->name }}</label>
                            <textarea class="form-control" name="empty[{{$occasion['location']->id}}][{{$occasion['day']->id}}]">{{ $occasion['replacement'] }}</textarea>
                        </div>
                    @endforeach

                    <br />
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    </div>
@endsection
