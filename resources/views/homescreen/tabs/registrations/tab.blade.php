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
                        {{ $service->getSeatfinder()->remainingCapacityText() }}
                    </td>
                    <td>
                        <a class="btn btn-sm btn-success" href="{{route('seatfinder', $service->id)}}" title="Anmeldung eintragen"><span class="fa fa-ticket-alt"></span> Neue Anmeldung</a>
                        <a class="btn btn-sm btn-secondary" href="{{route('service.bookings', $service->id)}}" title="Anmeldungen ansehen"><span class="fa fa-clipboard-list"></span> Anmeldungen</a>
                        @include('partials.service.edit-block', ['service', $service])
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
