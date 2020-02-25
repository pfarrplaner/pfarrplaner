@extends('layouts.app')

@section('title', 'Gottesdienstliste für den Gemeindebrief erstellen  (Schritt 2)')

@section('content')
    <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'render']) }}">
        @component('components.ui.card')
            @slot('cardHeader')Termine ohne Gottesdienst ergänzen @endslot
            <div class="card-body">
                @csrf
                <input type="hidden" name="start" value="{{ $start }}"/>
                <input type="hidden" name="end" value="{{ $end }}"/>
                @foreach ($includeCities as $city)
                    <input type="hidden" name="includeCities[]" value="{{ $city }}"/>
                @endforeach
                @foreach ($locationIds as $position => $location)
                    <input type="hidden" name="locations[{{ $position }}]" value="{{ $location }}"/>
                @endforeach
                @foreach ($dayList as $dayId)
                    <input type="hidden" name="dayList[]" value="{{ $dayId }}"/>
                @endforeach
                <div class="row">
                    <div class="col-md-2"></div>
                    @foreach ($locations as $location)
                        <div class="col-md-2">{{ $location->name }}</div>
                    @endforeach
                </div>
                @foreach($empty as $dayRecord)
                    <div class="row">
                        <div class="col-md-2">
                            <input type="checkbox" name="dayList[]" value="{{ $dayRecord['day']->id }}" checked>
                            {{ $dayRecord['day']->date->formatLocalized('%A, %d.%m.%Y') }}
                        </div>
                        @foreach($locations as $location)
                            <div class="col-md-2">
                                @if(isset($dayRecord['locations'][$location->id]))
                                    <div class="form-group">
                                    <textarea class="form-control"
                                              name="empty[{{$location->id}}][{{$dayRecord['day']->id}}]">{{ $dayRecord['locations'][$location->id] }}</textarea>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        @endcomponent
    </form>
@endsection
