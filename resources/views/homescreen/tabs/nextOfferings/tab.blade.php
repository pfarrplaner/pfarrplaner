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
            <th>Details</th>
            @canany(['gd-opfer-lesen','gd-opfer-bearbeiten'])
                <th>Opfer</th>
            @endcanany
            <th></th>
        </tr>
        </thead>
        <tbody>
        @foreach ($services as $service)
            <tr class="@if($service->day->date < \Carbon\Carbon::now())past @else future @endif">
                <td>{{ $service->day->date->format('d.m.Y') }}</td>
                <td>{{ $service->timeText() }}</td>
                <td>{{ $service->locationText() }}</td>
                <td>
                    @include('partials.service.details', ['service' => $service])
                </td>
                @canany(['gd-opfer-lesen','gd-opfer-bearbeiten'])
                    <td>
                        {{ $service->offering_goal }}<br />
                        <small>
                            Zähler 1: {{ $service->offering_counter1 }}<br />
                            Zähler 2: {{ $service->offering_counter2 }}</small>
                    </td>
                @endcanany
                <td>
                    @include('partials.service.edit-rites-block', ['service', $service])
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endtab
