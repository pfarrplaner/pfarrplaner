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
    @if(count($bookings))
        @component('components.ui.card')
            @slot('cardHeader')
                Anmeldungen ({{ $capacity }} / {{ $service->getSeatfinder()->maximumCapacity() }} Plätze frei)
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
