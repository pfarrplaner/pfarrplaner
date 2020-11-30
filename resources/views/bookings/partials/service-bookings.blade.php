<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Anzahl Personen</th>
            <th>Kontakt</th>
            @if($service->getSeatFinder()->hasSeats)<th>Platz*</th>@endif
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($service->bookings as $booking)
            <tr>
                <td>{{ $booking->name }}@if($booking->first_name), {{ $booking->first_name }}@endif</td>
                <td>{{ $booking->number }}</td>
                <td>{!!  nl2br($booking->contact) !!}</td>
                @if($service->getSeatFinder()->hasSeats)
                <td>
                    @include('bookings.partials.place', ['place' => $seating['list'][$booking->code], 'seating' => $seating, 'taken' => 1, 'auto' => ($booking->fixed_seat == '')])
                </td>
                @endif
                <td class="text-right">
                    <a class="btn btn-sm btn-secondary" title="Anmeldung bearbeiten" href="{{ route ('booking.edit', $booking) }}"><span class="fa fa-edit"></span></a>
                    <a class="btn btn-sm btn-danger btn-delete-booking" title="Anmeldung löschen" data-route="{{ route('booking.destroy', $booking->id) }}" style="color: white;"><span class="fa fa-trash"></span></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @if($service->getSeatFinder()->hasSeats)<small><b>*</b> <i>Kursiv</i> angegebene Plätze sind vorläufig und können sich noch ändern.</small>@endif
</div>
