<div class="table-responsive">
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Name</th>
            <th>Anzahl Personen</th>
            <th>Kontakt</th>
            <th>Code</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach($service->bookings as $booking)
            <tr>
                <td>{{ $booking->name }}@if($booking->first_name), {{ $booking->first_name }}@endif</td>
                <td>{{ $booking->number }}</td>
                <td>{!!  nl2br($booking->contact) !!}</td>
                <td>{{ $booking->code }}@if($booking->fixed_seat)<br /><span style="color: red;"><small>Fester Platz: {{ $booking->fixed_seat }}</small></span>@endif</td>
                <td class="text-right">
                    <a class="btn btn-sm btn-danger btn-delete-booking" title="Anmeldung löschen" data-route="{{ route('booking.destroy', $booking->id) }}" style="color: white;"><span class="fa fa-trash"></span></a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
