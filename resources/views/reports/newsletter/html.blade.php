<table>
    <thead></thead>
    <tbody>
    @foreach($events as $dateCode => $theseEvents)
        <?php
        $date = \Carbon\Carbon::createFromFormat('YmdHis', $dateCode)->setTime(0, 0, 0);
        $liturgy = \App\Liturgy::getDayInfo($date->format('d.m.Y'));
        ?>
        <tr>
            <td colspan="3"
                style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 12px 0 12px 0; color: #804070;">
                <strong>{{ $date->formatLocalized('%A, %d. %B %Y') }}</strong>
            </td>
        </tr>
        @foreach($theseEvents as $event)
            @if (is_object($event))
                <tr>
                    <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 30px 0 0;"
                        valign="top">
                        {{ $event->timeText(true, '.') }}</td>
                    <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 30px 0 0;"
                        valign="top">
                        <strong>{{ $event->titleText(false, false) }}</strong> ({{ $event->locationText() }})
                    </td>
                    <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0;"
                        valign="top">
                        <strong>{{ $event->participantsText('P') }}</strong></td>
                </tr>
            @else
                <tr>
                    <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 30px 0 0;"
                        valign="top">
                        {{ $event['start']->format('H.i') }} Uhr
                    </td>
                    <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0 30px 0 0;"
                        valign="top">
                        <strong>{{ $event['title'] }}</strong>@if($event['subtitle'] ?? '')
                            <br/>{{ $event['subtitle'] }} @endif
                        @if($event['locationtitle'] ?? '')({{ $event['locationtitle'] }})@endif
                    </td>
                    <td style="font-size: 12px; font-family: verdana, arial, helvetica, sans-serif; padding: 0;"
                        valign="top"></td>
                </tr>
            @endif
        @endforeach
    @endforeach
    </tbody>
</table>
