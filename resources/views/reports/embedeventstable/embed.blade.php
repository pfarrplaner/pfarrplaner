<style id="buttonstyles">
    .events-list-table tr.hoverable:hover {
        background-color: #B76FA5;
        cursor: pointer;
    }
    .events-list-table td {
        vertical-align: top;
    }
    .small-button {
        font-size: .8em;
        border: solid 1px darkgray;
        padding: 4px 7px;
        border-radius: 3px;
        color: black;
        cursor: pointer;
    }
    .youtube-button {
        font-size: 1.2em;
        border: solid 1px darkgray;
        padding: 4px 7px;
        border-radius: 3px;
        cursor: pointer;
    }
    .youtube-button .fab {
        color: red;
    }
    .youtube-button:hover, .small-button:hover {
        background-color: white;
        box-shadow: 0 5px 11px 0 rgba(0,0,0,0.18),0 4px 15px 0 rgba(0,0,0,0.15);
    }
</style>
<div id="{{ $randomId }}">
    <div id="{{ $randomId }}_table" class="events-list-table">
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

                        <?php $eventStart = is_array($event) ? $event['start'] : $event->date ?>
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
                                    <b>{{ $event->titleText(false, false) }}</b> @if($event->participantsText('P') != '')
                                        ({{ $event->participantsText('P') }})@endif
                                    @if($event->descriptionText())<br/>{{ $event->descriptionText() }}@endif
                                    @if($event->controlled_access) @component('components.service.controlledAccess', ['service' => $event]) @endcomponent @endif
                                    @if ($event->offering_goal)<br/>Opfer: {{ $event->offering_goal }}@endif
                                    <div>
                                    @if ($event->songsheet) <span class="small-button" href="{{ $event->songsheetUrl }}" title="Klicken Sie hier, um das Liedblatt herunterzuladen"><span class="fa fa-file-pdf"></span> Liedblatt</span> @endif
                                    @if ($event->offerings_url) <span class="small-button" href="{{ $event->offerings_url }}" title="Klicken Sie hier, um online zu spenden"><span class="fa fa-coins"></span> Opfer</span> @endif
                                    @if ($event->cc_streaming_url) <span class="small-button" href="{{ $event->cc_streaming_url }}"title="Klicken Sie hier, um den Kindergottesdienst auf YouTube anzuschauen"><img src="{{ asset('img/cc.png') }}" height="12px"> Kinderkirche</span> @endif
                                    @if ($event->external_url) <span class="small-button" href="{{ $event->external_url }}"  title="Klicken Sie hier, um zur Predigtseite zu gelangen"><span class="fa fa-globe"></span> Externe Seite zur Predigt</span> @endif
                                    </div>
                                </td>
                                <td valign="top" style="vertical-align:top;">
                                    @if (is_array($event)) @if(isset($event['place'])){{ $event['place'] }} @endif
                                    @else {{ $event->locationText() }} @if($event->youtube_url)
                                        <div><a class="youtube-button" target="_blank" href="{{ $event->youtube_url }}"><span class="fab fa-youtube"></span> Auf YouTube ansehen</a></div>
                                    @endif @if (is_object($event->location) && ($event->location->instructions != ''))<div>
                                                                           <small>{!! nl2br($event->location->instructions) !!}</small></div>@endif
                                    @endif
                                </td>
                            </tr>
                            @if ($event->cc)
                                <tr>
                                    <td valign="top"
                                        style="vertical-align:top;">{!! $eventStart->formatLocalized('%a.,&nbsp;%d.%m.') !!}</td>
                                    <td valign="top">{{ str_replace(':', '.', ($event->cc_alt_text ?? $event->timeText(true, '.'))) }}</td>
                                    <td valign="top">
                                        <b>Kinderkirche</b>
                                    </td>
                                    <td valign="top" style="vertical-align:top;">
                                        {{ $event->cc_location ?? $event->locationText() }}
                                    </td>
                                </tr>
                            @endif
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
    $('<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" />').appendTo("head");
    $("<style type='text/css'>" + $('#buttonstyles').html() + "</style>").appendTo("head");

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
