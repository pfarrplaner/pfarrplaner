@tab(['id' => $tab->getKey(), 'active' => ($index == 0)])
<table class="table table-striped">
    <thead>
    <tr>
        <th>Datum</th>
        <th>Zeit</th>
        <th>Ort</th>
        <th>Details</th>
        <th>Fehlende Angaben</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @foreach ($missing as $service)
        <tr>
            <td>{{ $service->day->date->format('d.m.Y') }}</td>
            <td>{{ $service->timeText() }}</td>
            <td>{{ $service->locationText() }}</td>
            <td> @include('partials.service.details', ['service' => $service])</td>
            <td>@foreach($config['ministries'] as $ministry)@if($service->participantsText($ministry) == '')
                    <span class="badge badge-info">{{ $ministry }}</span>@endif @endforeach</td>
            <td>
                @include('partials.service.edit-rites-block', ['service' => $service])
            </td>
        </tr>
    @endforeach
    </tbody>
</table>
@endtab
