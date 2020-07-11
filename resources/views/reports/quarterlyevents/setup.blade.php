@extends('layouts.app')

@section('title', 'Quartalsprogramm erstellen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Quartalsprogramm erstellen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('reports.render', $report) }}">
                    @csrf
                    <div class="form-group">
                        <label class="control-label" for="title">Titel:</label>
                        <input class="form-control" name="title" value="{{ Auth::user()->getSetting('quarterly_events_report_title', '') }}" />
                    </div>
                    <div class="form-group">
                        <label class="control-label">Liste für folgenden Veranstaltungsort:</label>
                        <select class="form-control" name="location">
                            @foreach ($locations as $location)
                                <option value="{{ $location->id }}">
                                    {{$location->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="start">Quartal:</label>
                        <select class="form-control" name="quarter">
                            @while($minDate <= $maxDate)
                                <option value="{{$minDate->firstOfQuarter()->format('Y-m-d')}}">{{ $minDate->quarter }} / {{$minDate->format('Y')}}</option>
                                <?php $minDate->addMonth(3); ?>
                            @endwhile
                        </select>
                    </div>
                    <div class="form-group"> <!-- Radio group !-->
                        <label class="control-label">Folgende Informationen mit einbeziehen:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="includePastor" value="1" checked >
                                <label class="form-check-label" for="includePastor">Pfarrer*in</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="includeOrganist" value="1" checked >
                                <label class="form-check-label" for="includeOrganist">Organist*in</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="includeSacristan" value="1" checked >
                                <label class="form-check-label" for="includeSacristan">Mesner*in</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="includeDescription" value="1" checked >
                                <label class="form-check-label" for="includeDescription">Besonderheiten</label>
                            </div>
                    </div>
                    <div class="form-group">
                        <label for="notes1">Hinweise <i>vor</i> der Übersicht der Gottesdienste:</label>
                        <textarea class="form-control" name="notes1">{{ Auth::user()->getSetting('quarterly_events_report_notes1', '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="notes2">Hinweise <i>nach</i> der Übersicht der Gottesdienste:</label>
                        <textarea class="form-control" name="notes2">{{ Auth::user()->getSetting('quarterly_events_report_notes2', '') }}</textarea>
                    </div>
                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="includeContact" value="1" checked >
                            <label class="form-check-label" for="includeContact">Meine Kontaktdaten für weitere Informationen mit aufnehmen</label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
