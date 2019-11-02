@if(count($events))
    <h3>Termine der nächsten {{ $days }} Tage</h3>
    <table class="ce-table">
        <thead>
        <th>Datum</th>
        <th>Uhrzeit</th>
        <th>Veranstaltung</th>
        <th>Ort</th>
        </thead>
        <tbody>
        <?php $lastDate = ''; ?>
        @foreach ($events as $theseEvents)
            @foreach($theseEvents as $event)
                <?php $eventStart = is_array($event) ? $event['start'] : $event->day->date ?>
                <tr>
                    @if ($lastDate != $eventStart->format('Ymd') )
                        <?php $lastDate = $eventStart->format('Ymd') ?>
                        <td>{{ $eventStart->formatLocalized('%a, %d.%m.') }}</td>
                    @else
                        <td></td>
                    @endif
                    <td>@if (is_array($event)) {{ $eventStart->formatLocalized('%H.%M Uhr') }} @else{{ $event->trueDate()->formatLocalized('%H.%M Uhr') }}@endif</td>
                    <td>
                        @if (is_array($event))<b>{{ $event['title'] }}</b>@else
                            <b>Gottesdienst</b> ({{ $event->participantsText('P') }})
                            @if($event->descriptionText())<br />{{ $event->descriptionText() }}@endif
                            @if ($event->offering_goal)<br />Opfer: {{ $event->offering_goal }}@endif
                        @endif
                    </td>
                    <td>
                        @if (is_array($event)){{ $event['place'] }}@else {{ $event->locationText() }}@endif
                    </td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
@endif