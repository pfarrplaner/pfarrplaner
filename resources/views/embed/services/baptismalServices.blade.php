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
        <th>Taufmöglichkeit</th>
    @endif
    </thead>
    <tbody>
    <?php $lastDate = ''; ?>
    @foreach($services as $service)
        <tr>
            @if ($lastDate != $service->day->date->format('d.m.') )
                <td>{{ $lastDate = $service->day->date->format('d.m.') }}</td>
            @else
                <td></td>
            @endif
            <td>{{ $service->timeText() }}</td>
            @if (!isset($locationIds) || count($locationIds) >1)
                <td>{{ $service->locationText() }}</td>
            @endif
            <td>{{ $service->participantsText('P') }}</td>
@if ($maxBaptisms >0 )
            <td @if(count($service->baptisms) == 0) style="background-color: limegreen;" @elseif(count($service->baptisms) < $maxBaptisms) style="background-color: yellow" @else style="background-color: red" @endif>
                @if(count($service->baptisms) == 0) Taufanmeldung möglich
                @elseif(count($service->baptisms) < $maxBaptisms) Taufanmeldung möglich (bereits {{ count ($service->baptisms) }} {{ count ($service->baptisms) == 1 ? 'Taufe' : 'Taufen' }})
                @else Taufanmeldung nicht mehr möglich @endif
            </td>
@endif
        </tr>
    @endforeach
    </tbody>
</table>
@endif