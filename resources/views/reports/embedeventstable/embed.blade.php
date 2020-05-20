<style>
    #{{ $randomId }} tr.hoverable:hover {
        background-color: #B76FA5;
        cursor: pointer;
    }
</style>
<div id="{{ $randomId }}">
    <div id="{{ $randomId }}_table">
        @if(count($events))
            <h3>Termine der nächsten {{ $days }} Tage</h3>
            <table class="ce-table striped">
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
                        @if ($lastDate != $eventStart->format('Ymd') )
                            <?php
                            $lastDate = $eventStart->format('Ymd');
                            $liturgy = \App\Liturgy::getDayInfo($eventStart->format('d.m.Y'));
                            ?>
                            @if($liturgy['title'] ?? '')
                                <tr style="background-color: #ccc !important;">
                                    <td valign="top"
                                        style="vertical-align:top;">{!! $eventStart->formatLocalized('%a.,&nbsp;%d.%m.') !!}</td>
                                    <td></td>
                                    <td valign="top" colspan="2"
                                        style="vertical-align:top; font-weight: bold;">{{ ucfirst(str_replace('So.', 'Sonntag', $liturgy['title'])) }}</td>
                                </tr>
                            @endif
                        @endif
                        @if (!is_object($event))
                            @include('reports.embedeventstable.parts.op_event')
                        @else
                            <tr>
                                <td valign="top"
                                    style="vertical-align:top;">{!! $eventStart->formatLocalized('%a.,&nbsp;%d.%m.') !!}</td>
                                <td valign="top">{{ $event->timeText(true, '.') }}</td>
                                <td valign="top">
                                    <b>{{ ($event->title ?: 'Gottesdienst') }}</b> @if($event->participantsText('P') != '')
                                        ({{ $event->participantsText('P') }})@endif
                                    @if($event->descriptionText())<br/>{{ $event->descriptionText() }}@endif
                                    @if ($event->offering_goal)<br/>Opfer: {{ $event->offering_goal }}@endif
                                </td>
                                <td valign="top" style="vertical-align:top;">
                                    @if (is_array($event)) @if(isset($event['place'])){{ $event['place'] }} @else @dd ($event) @endif @else {{ $event->locationText() }}@endif
                                </td>
                            </tr>
                        @endif
                        @endforeach
                    @endforeach
                </tbody>
            </table>
    </div>
    <div id="{{ $randomId }}_details" style="display:none;">
        <div id="{{ $randomId }}_details_content"></div>
        <hr/>
        <div>
            <a href="" class="back-link"
               onclick="back_{{ $randomId }}(event)">&lt;
                Zurück zur Übersicht</a>
        </div>
    </div>
</div>
<script defer>
    function back_{{ $randomId }}(event) {
        event.preventDefault();
        $('#{{ $randomId }}_details').hide();
        $('#{{ $randomId }}_table').show();
        var hash = document.location.hash;
        document.location.hash = '';
        hash = hash.replace('#', '#{{ $randomId }}_') + '_row';
        $(hash).scrollIntoView();
    }


    $('<style type="text/css"></style>').appendTo('head');
    $('#{{ $randomId }} tr.clickable').addClass('hoverable');
    $('#{{ $randomId }} tr.clickable').hover(function () {
        $(this).css('cursor', 'pointer');
    }, function () {
        $(this).css('cursor', 'inherit');
    });
    $('#{{ $randomId }} tr.clickable').click(function () {
        document.location.hash = $(this).data('id');
        $('#{{ $randomId }}_details_content').html($(this).find('div.details').first().html());
        $('#{{ $randomId }}_details').show();
        $('#{{ $randomId }}_table').hide();
        window.scrollTo(0, 0);
    });

    if ((document.location.hash != '') && (document.location.hash != '#')) {
        t = document.location.hash;
        if (t.charAt(0) == '#') t = t.substr(1);
        t = '#{{ $randomId }}_' + t;
        $('#{{ $randomId }}_details_content').html($(t).html());
        $('#{{ $randomId }}_details').show();
        $('#{{ $randomId }}_table').hide();
    }
</script>
@endif
