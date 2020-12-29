@tab(['id' => $tab->getKey(), 'active' => ($index == 0)])
@if(count($registrable))
    <div class="table-responsive">
        <table class="table table-striped" width="100%">
            <thead>
            <tr>
                <th>Datum</th>
                <th>Zeit</th>
                <th>Ort</th>
                <th>Details</th>
                <th>Plätze</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($registrable as $service)
                <tr>
                    <td>{{ $service->day->date->format('d.m.Y') }}</td>
                    <td>{{ $service->timeText() }}</td>
                    <td>{{ $service->locationText() }}</td>
                    <td>
                        @include('partials.service.details', ['service', $service])
                    </td>
                    <td>
                        {{ $service->getSeatfinder()->freeSeatsText() }}
                    </td>
                    <td>
                        <a class="btn btn-sm btn-success" href="{{route('seatfinder', $service->id)}}" title="Anmeldung eintragen"
                       @if(!$service->registration_active) onclick="return confirm('Die Anmeldung für diesen Gottesdienst ist momentan deaktiviert. Möchtest du wirklich trotzdem eine neue Anmeldung anlegen?');" @endif>
                        <span class="fa fa-ticket-alt"></span> Neue Anmeldung</a>
                        <a class="btn btn-sm btn-secondary" href="{{route('service.bookings', $service->id)}}" title="Anmeldungen ansehen"
                        ><span class="fa fa-clipboard-list"></span> Anmeldungen</a>
                        @if($service->needs_reservations && (!$service->registration_active))<div class="alert alert-warning">Anmeldung momentan deaktiviert.</div> @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@else
    <p>Zur Zeit gibt es keine Gottesdienste, für die Anmeldungen angenommen werden.</p>
@endif
@endtab
