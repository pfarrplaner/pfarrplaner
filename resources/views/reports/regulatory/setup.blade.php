@extends('layouts.app')

@section('content')
    <form method="post" action="{{ route('reports.render', 'regulatory') }}">
        @csrf
        @component('components.ui.card')
            @slot('cardHeader')
                Meldung an das Ordnungsamt nach §1g Abs. 3 CoronaVO
            @endslot
            @slot('cardFooter')
                <button class="btn btn-primary">Weiter &gt;</button>
            @endslot
            <div class="form-group">
                <label for="service">Meldung für folgenden Gottesdienst absenden:</label>
                <select id="service" class="form-control" name="service">
                    @foreach($services as $service)
                        <option data-number="{{ $service->estimatePeoplePresent() }}" @if($service->id == $preselectedService) selected @endif value="{{ $service->id }}">
                            {{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText() }}, {{ $service->locationText() }}, {{ $service->titleText() }} [{{ $service->estimatePeoplePresent() }}]
                        </option>
                    @endforeach
                </select>
                @input(['id' => 'number', 'name' => 'number', 'type' => 'number', 'label' => 'Voraussichtliche Teilnehmerzahl', 'value' => 0])
                @input(['name' => 'email', 'label' => 'E-Mailadresse des Ordnungsamts', 'type' => 'email', 'value' => $recipient])
                @textarea(['name' => 'template', 'label' => 'Text der Nachricht', 'value' => $template])
                <small>In der Nachricht können folgende Platzhalter verwendet werden, die automatisch ersetzt werden: <br />
                    ###GOTTESDIENST### Daten des Gottesdiensts<br />
                    ###TEILNEHMERZAHL### Voraussichtliche Anzahl der Teilnehmer<br />
                    ###KIRCHENGEMEINDE### Name der Kirchengemeinde<br />
                </small>
            </div>
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        function updateEstimatedNumber() {
            $('input[name=number]').val($('#service option:selected').data('number'));
        }

        $(document).ready(function(){
            $('#service').on('input', function(){
                updateEstimatedNumber();
            });
            updateEstimatedNumber();
        });
    </script>
@endsection
