@extends('layouts.app')

@section('title', 'Gottesdienstliste für den Gemeindebrief erstellen  (Schritt 2)')

@section('content')
    <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'empty']) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <input type="hidden" name="start" value="{{ $start }}"/>
                <input type="hidden" name="end" value="{{ $end }}"/>
                @foreach ($includeCities as $city)
                    <input type="hidden" name="includeCities[]" value="{{ $city }}"/>
                @endforeach
                <div class="row">
                    @for($i=0; $i<4; $i++)
                        <div class="col-3">
                            <div class="form-group">
                                <label for="locations[{{ $i }}]">Spalte {{ $i+1 }}</label>
                                <select class="form-control fancy-selectize" name="locations[{{ $i }}]">
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}"
                                                @if($presets[$i] == $location->id) selected @endif>{{ $location->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endfor
                </div>


            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Weiter ></button>
            </div>
        </div>
    </form>
@endsection
