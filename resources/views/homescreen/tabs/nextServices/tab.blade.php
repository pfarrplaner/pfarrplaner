@tab(['id' => $tab->getKey(), 'active' => ($index == 0)])
    <h2>{{ $tab->getTitle() }}</h2>
    <p>Angezeigt werden die Gottesdienste der nächsten 2 Monate</p>
    <div class="table-responsive">
        <table class="table table-striped" width="100%">
            <thead>
            <tr>
                <th>Datum</th>
                <th>Zeit</th>
                <th>Ort</th>
                <th>Liturgische Infos</th>
                <th>Details</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($services as $service)
                <tr>
                    <td>{{ $service->day->date->format('d.m.Y') }}</td>
                    <td>{{ $service->timeText() }}</td>
                    <td>{{ $service->locationText() }}</td>
                    <td>
                        @if(isset($service->day->liturgy['title'])){{ $service->day->liturgy['title'] }}<br />@endif
                        @if(isset($service->day->liturgy['currentPerikopeLink']))
                            <a href="{{$service->day->liturgy['currentPerikopeLink']}}" target="_blank">
                                {{ $service->day->liturgy['currentPerikope'] }}
                            </a><br />@endif
                    </td>
                    <td>
                        @include('partials.service.details')
                    </td>
                    <td>
                        @include('partials.service.edit-rites-block', ['service', $service])
                        @if($service->needs_reservations && (!$service->registration_active))<div class="alert alert-warning">Anmeldung momentan deaktiviert.</div> @endif
                    </td>
                </tr>
                @dd($service->credits)
            @endforeach
            </tbody>
        </table>
    </div>
@endtab
