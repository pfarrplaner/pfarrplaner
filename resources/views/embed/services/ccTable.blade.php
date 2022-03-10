@if(count($services))
@if($title) <h3>{{ $title }}</h3> @endif
<table class="ce-table">
    <thead>
    <th>Datum</th>
    <th>Uhrzeit</th>
    @if (!isset($locationIds) || count($locationIds) >1)
        <th>Kirche</th>
    @endif
    <th></th>
    </thead>
    <tbody>
    <?php $lastDate = ''; ?>
    @foreach($services as $service)
        <tr>
            @if ($lastDate != $service->date->format('d.m.') )
                <td>{{ $lastDate = $service->date->format('d.m.') }}</td>
            @else
                <td></td>
            @endif
            <td>{{ $service->timeText() }}</td>
            @if (!isset($locationIds) || count($locationIds) >1)
                <td>{{ $service->locationText() }}</td>
            @endif
            <td>{{ $service->cc_lesson }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
@endif
