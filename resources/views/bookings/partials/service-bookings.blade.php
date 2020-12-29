<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Anzahl Personen</th>
            <th>Kontakt</th>
            <th>Zeit der Anmeldung</th>
            @if($service->getSeatFinder()->hasSeats)<th>Platz*</th>@endif
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($service->bookings->sortBy('created_at') as $booking)
            <tr>
                <td>{{ $booking->name }}@if($booking->first_name), {{ $booking->first_name }}@endif</td>
                <td>{{ $booking->number }}</td>
                <td>{!!  nl2br($booking->contact) !!}@if ($booking->email)<br />{{ $booking->email }}@endif</td>
                <td>{{ $booking->created_at->format('d.m.Y, H:i') }} Uhr</td>
                @if($service->getSeatFinder()->hasSeats)
                <td>
                    @include('bookings.partials.place', ['place' => $seating['list'][$booking->code], 'seating' => $seating, 'taken' => 1, 'auto' => ($booking->fixed_seat == ''), 'number' => $booking->number])
                    @if($booking->fixed_seat == '' && (is_object($service->location)) && (count($service->location->seatingSections) > 0) && (isset($seating['grid'][$seating['list'][$booking->code]])))
                        <form method="post" class="form-inline" action="{{ route('booking.update', $booking) }}" style="display: inline;">
                            @method('PATCH')
                            @hidden(['name' => 'name', 'value' => $booking->name])
                            @hidden(['name' => 'first_name', 'value' => $booking->first_name])
                            @hidden(['name' => 'contact', 'value' => $booking->contact])
                            @hidden(['name' => 'number', 'value' => $booking->number])
                            @hidden(['name' => 'email', 'value' => $booking->email])
                            @hidden(['name' => 'service_id', 'value' => $service->id])
                            @hidden(['name' => 'fixed_seat', 'value' => $seating['list'][$booking->code]])
                            @hidden(['name' => 'override_seats', 'value' => ''])
                            @hidden(['name' => 'override_split', 'value' => ''])
                            @csrf
                            <button class="btn btn-xs btn-secondary" title="Platz festlegen"><span class="fa fa-thumbtack"></span></button>
                        </form>
                    @endif
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
