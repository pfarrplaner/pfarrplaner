@extends('layouts.app')

@section('title', 'Anmeldungen für den Gottesdienst am '.$service->day->date->format('d.m.Y').' um '.$service->timeText())

@section('navbar-left')
    @if($capacity >0)
        <a class="btn btn-success" href="{{ route('seatfinder', $service->id) }}">
            <span class="fa fa-ticket-alt"></span> Neue Anmeldung
        </a>&nbsp;
    @else
        <a href="#" class="btn btn-secondary">Keine weitere Anmeldung möglich.</a>&nbsp;
    @endif
    <a class="btn btn-secondary" href="{{ route('booking.finalize', $service->id) }}">
        <span class="fa fa-clipboard-check"></span> Liste drucken
    </a>
@endsection

@section('content')
    @if ($service->exclude_sections)<p><b>Gesperrte Bereiche:</b> {{ $service->exclude_sections }}</p> @endif
    @if ($service->exclude_places)<p><b>Gesperrte Plätze:</b>
        @foreach(explode(',', $service->exclude_places) as $place) @include('bookings.partials.place', ['place' => $place, 'seating' => $seating]) @endforeach
    </p>@endif
    @if ($service->reserved_places)<p><b>Zurückgehaltene Plätze:</b>
    @foreach(explode(',', $service->reserved_places) as $place) @include('bookings.partials.place', ['place' => $place, 'seating' => $seating]) @endforeach
    </p>@endif
    @if (isset($seating['free']) && count($seating['free']))<p><b>Freie Plätze:</b>
        @foreach ($seating['free'] as $place) @include('bookings.partials.place', ['place' => $place, 'seating' => $seating]) @endforeach
    </p>@endif
    @if(isset($seating['count']))
        <p><b>Zahl der angemeldeten Personen:</b> {{ $seating['count'] }} @if(isset($seating['load'])) (Raumnutzung: {{ (int)$seating['load'] }}%) @endif
        </p>
    @endif
    @if(count($bookings))
        @component('components.ui.card')
            @slot('cardHeader')
                Anmeldungen ({{ $service->getSeatfinder()->freeSeatsText() }} Plätze frei)
            @endslot
            @include('bookings.partials.service-bookings')
        @endcomponent
    @else
        <p>Es liegen noch keine Anmeldungen vor.</p>
    @endif
@endsection

@section('scripts')
    <script>
    $(document).ready(function(){
        $('.btn-delete-booking').click(function(e) {
            e.preventDefault();
            if (confirm('Soll diese Anmeldung wirklich gelöscht werden?')) {
                fetch($(this).data('route'), {
                    method: 'DELETE',
                    body: '_method=DELETE',
                    headers: {
                        "X-CSRF-Token": window.Laravel.csrfToken
                    }
                })
                    .then(res => {
                        $(this).parent().parent().remove();
                    })
            }
        });
    });
    </script>
@endsection
