@if(isset($service))
    @if(null !== $service)
        <a class="btn btn-sm btn-secondary" href="{{ route('calendar', $service->day->date->format('Y-m')) }}" title="Im Kalender ansehen"><span class="fa fa-calendar"></span></a>
        @can('update', $service)
            <a class="btn btn-sm btn-primary" href="{{route('service.edit', ['service' => $service->slug, 'tab' => 'rites'])}}?back=/home" title="Gottesdienst bearbeiten"><span class="fa fa-edit"></span></a>
        @endcan
        @if(is_object($service->location) && count($service->location->seatingSections))
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
        <div class="mt-1">
            <a class="btn btn-secondary" title="Meldung an das Ordnungsamt nach §1g Abs. 3 CoronaVO" href="{{ route('reports.setup', ['report' => 'regulatory', 'service' => $service->id]) }}">
                <span class="fa fa-envelope"></span> Ordnungsamt <span class="badge badge-info">{{ $service->estimatePeoplePresent() }}</span></a>
        </div>
        @if(\App\Integrations\KonfiApp\KonfiAppIntegration::isActive($service->city) && ($service->konfiapp_event_qr != ''))
            <div style="margin-top: 2px;">
                <a class="btn btn-sm btn-secondary" href="{{ route('report.step', ['report' => 'KonfiAppQR', 'step' => 'single', 'service' => $service->id]) }}" title="QR-Code drucken">
                    <span class="fa fa-qrcode"></span>
                </a>
            </div>
        @endif
    @endif
@endif
