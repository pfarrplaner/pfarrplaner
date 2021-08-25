<a class="btn btn-sm btn-secondary"
   href="{{ route('calendar', $service->day->date->format('Y-m')) }}"
   title="Im Kalender ansehen"><span class="fa fa-calendar"></span></a>
@can('update', $service)
    <a class="btn btn-sm btn-primary" href="{{route('service.edit', $service->id)}}" title="Bearbeiten"><span
            class="fa fa-edit"></span></a>
@endcan
    @if(($service->needs_reservations) || (is_object($service->location) && count($service->location->seatingSections)))
        <br/>
        <div style="margin-top: 2px;">
            <a class="btn btn-sm btn-success" href="{{ route('seatfinder', $service) }}" title="Neue Anmeldung"
            @if(!$service->registration_active) onclick="return confirm('Die Anmeldung für diesen Gottesdienst ist momentan deaktiviert. Möchtest du wirklich trotzdem eine neue Anmeldung anlegen?');" @endif>
                <span class="fa fa-ticket-alt"></span>
            </a>
            <a class="btn btn-sm btn-secondary" href="{{ route('service.bookings', $service) }}" title="Anmeldungen">
                <span class="fa fa-ticket-alt"></span>
            </a>
            <a class="btn btn-sm btn-secondary" href="{{ route('booking.finalize', $service) }}"
               title="Sitzplatzliste drucken">
                <span class="fa fa-clipboard-check"></span>
            </a>
        </div>
    @endif
