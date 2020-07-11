@if(count($missing))
    @tab(['id' => 'missing'])
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
        @foreach ($missing as $record)
            <tr>
                <td>{{ $record['service']->day->date->format('d.m.Y') }}</td>
                <td>{{ $record['service']->timeText() }}</td>
                <td>{{ $record['service']->locationText() }}</td>
                <td> @include('partials.service.details', ['service' => $record['service']])</td>
                <td> @badges(['items' => $record['missing'], 'badge_type' => 'info'])</td>
                <td>
                    @include('partials.service.edit-rites-block', ['service' => $record['service']])
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
    @endtab
@endif
