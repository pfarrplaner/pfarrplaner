    <a class="btn btn-sm btn-secondary" href="{{ route('calendar', ['month' => $service->day->date->format('m'), 'year' => $service->day->date->format('Y')]) }}" title="Im Kalender ansehen"><span class="fa fa-calendar"></span></a>
@can('update', $service)
    <a class="btn btn-sm btn-primary" href="{{route('services.edit', $service->id)}}" title="Bearbeiten"><span class="fa fa-edit"></span></a>
    @if(is_object($service->location) && count($service->location->seatingSections))
    <a class="btn btn-sm btn-secondary" href="{{ route('booking.finalize', $service) }}" title="Sitzplatzliste drucken">
        <span class="fa fa-clipboard-check"></span>
    </a>
    @endif
@endcan
