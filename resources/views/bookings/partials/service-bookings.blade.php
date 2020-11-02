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
                <td>{{ $booking->code }}</td>
                <td class="text-right">
                    <form class="form-inline" method="post" action="{{ route('booking.destroy', $booking->id) }}">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-sm btn-danger" title="Anmeldung löschen"><span class="fa fa-trash"></span></button>
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
