@if(count($services))
@if($title) <h3>{{ $title }}</h3> @endif
<table class="ce-table">
    <thead>
    <th>Datum</th>
    <th>Uhrzeit</th>
    @if (!isset($locationIds) || count($locationIds) >1)
        <th>Kirche</th>
    @endif
    <th>Pfarrer</th>
    @if ($maxBaptisms >0 )
        <th colspan="2">Taufmöglichkeit</th>
    @endif
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
            <td>{{ $service->participantsText('P') }}</td>
@if ($maxBaptisms >0 )
            <td style="padding: 0; font-size: 30pt; font-weight: bold;">
                @if(count($service->baptisms) == 0) <span style="color: limegreen;">&bull;</span>
                @elseif(count($service->baptisms) < $maxBaptisms) <span style="color: orange;">&bull;</span>
                @else <span style="color: red;">&bull;</span> @endif
            </td>
            <td>
                @if(count($service->baptisms) == 0)Taufanmeldung möglich
                @elseif(count($service->baptisms) < $maxBaptisms)Taufanmeldung möglich <br /><small>(bereits {{ count ($service->baptisms) }} {{ count ($service->baptisms) == 1 ? 'Taufe' : 'Taufen' }})</small>
                @else Taufanmeldung nicht mehr möglich @endif
            </td>
@endif
        </tr>
    @endforeach
    </tbody>
</table>
@endif
