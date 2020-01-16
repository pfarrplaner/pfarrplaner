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
                        <tr @if (is_array($event) && ($event['record_type'] == 'OP_Event'))class="clickable" data-id="{{ $event['event_id'] }}" id="{{ $randomId }}_{{ $event['event_id'] }}_row" title="Klicken, um mehr zu erfahren"@endif>
                            @if ($lastDate != $eventStart->format('Ymd') )
                                <?php $lastDate = $eventStart->format('Ymd') ?>
                                <td valign="top"
                                    style="vertical-align:top;">{{ $eventStart->formatLocalized('%a, %d.%m.') }}</td>
                            @else
                                <td valign="top" style="vertical-align:top;"></td>
                            @endif
                            <td valign="top" style="vertical-align:top;">@if (is_array($event))
                                    {{ $eventStart->formatLocalized('%H.%M Uhr') }}
                                @else{{ $event->trueDate()->formatLocalized('%H.%M Uhr') }}@endif</td>
                            <td valign="top" style="vertical-align:top;">
                                @if (!is_object($event))
                                    <div class="list-text" style="float: left; width: 70%;">
                                        <b>{{ $event['title'] }}</b>
                                        @if(isset($event['teaser']))<br/>{!! $event['teaser'] !!}@endif
                                    </div>
                                    @if(isset($event['event_images'][0]['file_id']))
                                        <div class="gp-image" style="float: left; width: 20%; padding-right: 1.5%;">
                                            <img src="https://backend.online-geplant.de/public/file/embed/{{ $customerToken }}/{{ $customerKey }}/{{ $event['event_images'][0]['file_id'] }}"
                                                 style="width: 100%;"
                                                 alt="{{$event['event_images'][0]['alttext']}}"
                                                 title="{{$event['event_images'][0]['text']}}">
                                            @if($event['event_images'][0]['source'])
                                                <span class="copyright">{{$event['event_images'][0]['source']}}</span>
                                            @endif
                                        </div>
                                    @endif
                                    @if ($event['record_type'] == 'OP_Event')
                                        <div class="details" style="display:none;" id="{{ $randomId }}_{{ $event['event_id'] }}">
                                            <div class="gp-wrapper">
                                                <div class="gp-detail-view">
                                                    <div class="event-headline" style="padding: 3%;">
                                                        <h1>{{ $event['title'] }}</h1>
                                                        @if(isset($event['subtitle']))
                                                            <h3>{{ $event['subtitle'] }}</h3>@endif
                                                        @if(isset($event['teaser'])){!! $event['teaser'] !!}@endif
                                                    </div>
                                                    <dl style="padding: 3%;">
                                                        <dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;"><b>@if(count($event['event_dates']) > 1)Termine:@else Termin:@endif</b></dt>
                                                        <dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">
                                                            @foreach($event['event_dates'] as $date)
                                                                @if(isset($date['start']))
                                                                    <span @if($date['eventdate_id'] == $event['eventdate_id']) style="font-weight: bold;" @endif>
                                                                    {{ $date['start']->formatLocalized('%d.%m.%Y, %H:%M Uhr') }}
                                                                    @if (isset($date['end']))
                                                                        - @if($date['startyearmonthdate'] != $date['endyearmonthdate'])
                                                                            {{ $date['end']->formatLocalized('%d.%m.%Y, %H:%M Uhr') }}
                                                                        @else
                                                                            {{ $date['end']->formatLocalized('%H:%M Uhr') }}
                                                                      @endif
                                                                    @endif
                                                                    </span>
                                                                @endif
                                                            @endforeach
                                                        </dd>
                                                    </dl>
                                                    @if(isset($event['event_images'][0]['file_id']))
                                                        <div class="details-item">
                                                            <img src="https://backend.online-geplant.de/public/file/embed/{{ $customerToken }}/{{ $customerKey }}/{{ $event['event_images'][0]['file_id'] }}"
                                                                 style="max-width: 100%; max-height: 450px;"
                                                                 alt="{{$event['event_images'][0]['alttext']}}"
                                                                 title="{{$event['event_images'][0]['text']}}">
                                                            @if($event['event_images'][0]['source'])
                                                                <span class="copyright">{{$event['event_images'][0]['source']}}</span>
                                                            @endif
                                                        </div>
                                                    @endif
                                                    @if(isset($event['description']))
                                                        <div class="event-text" style="padding:3%;">{!! $event['description'] !!}</div>
                                                    @endif
                                                    <dl style="padding: 3%;">
                                                        @if(isset($event['facilityorganizer']))
                                                        <dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Veranstalter:</dt>
                                                        <dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;"> {{$event['facilityorganizer']}}
                                                            @if(isset($event['facilitystreet']))<br/>{{ $event['facilitystreet']}} @endif
                                                            @if(isset($event['facilitycity']))<br/>{{ $event['facilityzip']}} {{ $event['facilitycity']}} @endif
                                                            @if(isset($event['facilityphone']))<br/>Fon: {{ $event['facilityphone']}} @endif
                                                            @if(isset($event['facilitfax']))<br/>Fax: {{ $event['facilitfax']}} @endif
                                                            @if(isset($event['facilityemail']))<br/>E-Mail: {{ $event['facilityemail']}} @endif
                                                            @if(isset($event['facilitywww']))<br/>Homepage: <a href="https://{{ $event['facilitywww']}}" target="_blank">{{ $event['facilitywww']}}</a> @endif
                                                        </dd>
                                                        @endif
                                                        @if(isset($event['locationtitle']))
                                                        <dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Ort:</dt>
                                                        <dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;"> {{$event['locationtitle']}}
                                                            @if(isset($event['locationlocation']))<br/>Raum: {{ $event['locationlocation']}} @endif
                                                            @if(isset($event['locationstreet']))<br/>{{ $event['locationstreet']}} @endif
                                                            @if(isset($event['locationcity']))<br/>{{ $event['locationzip']}} {{ $event['locationcity']}} @endif
                                                            @if(isset($event['locationphone']))<br/>Fon: {{ $event['locationphone']}} @endif
                                                            @if(isset($event['facilitfax']))<br/>Fax: {{ $event['facilitfax']}} @endif
                                                            @if(isset($event['locationemail']))<br/>E-Mail: {{ $event['locationemail']}} @endif
                                                            @if(isset($event['locationwww']))<br/>Homepage: <a href="https://{{ $event['locationwww']}}" target="_blank">{{ $event['locationwww']}}</a> @endif
                                                        </dd>
                                                        @endif
                                                        @if(isset($event['audience']))<dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Zielgruppe</dt><dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">{{ $event['audience'] }}</dd>@endif
                                                        @if(isset($event['participantsmax']) && ($event['participantsmax'] > 0))<dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Max. Teilnehmerzahl</dt><dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">{{ $event['participantsmax'] }}</dd>@endif
                                                        @if(isset($event['participantsnotice']))<dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Hinweise zur Teilnahme</dt><dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">{{ $event['participantsnotice'] }}</dd>@endif
                                                        @if(isset($event['price']))<dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Gebühr</dt><dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">{{ $event['price'] }} &euro; @if(isset($event['pricenotice'])){{ $event['pricenotice'] }}@endif</dd>@endif
                                                        @if(isset($event['course_number']))<dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Kursnummer</dt><dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">{{ $event['course_number'] }}</dd>@endif
                                                        @if(isset($event['course_lead']))<dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Leitung</dt><dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">{{ $event['course_lead'] }}</dd>@endif
                                                        @if(isset($event['instructors']))<dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Referent(en)</dt><dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">{{ $event['instructors'] }}</dd>@endif
                                                        @if(isset($event['requirements']))<dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Voraussetzungen</dt><dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">{{ $event['requirements'] }}</dd>@endif
                                                        @if(isset($event['contact']))<dt style="display: inline-block; width: 29%; vertical-align:top; margin-bottom: 10px;">Kontakt</dt>
                                                            <dd style="display: inline-block; width: 65%; vertical-align:top; margin-bottom: 10px;">{{ $event['contact'] }}
                                                                @if (isset($event['phone']))<br />Fon: {{ $event['phone'] }}@endif
                                                                @if (isset($event['email']))<br />E-Mail: <a href="mailto:{{ $event['email'] }}">{{ $event['email'] }}</a>@endif
                                                            </dd>
                                                        @endif
                                                    </dl>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <b>Gottesdienst</b> @if($event->participantsText('P') != '')
                                        ({{ $event->participantsText('P') }})@endif
                                    @if($event->descriptionText())<br/>{{ $event->descriptionText() }}@endif
                                    @if ($event->offering_goal)<br/>Opfer: {{ $event->offering_goal }}@endif
                                @endif
                            </td>
                            <td valign="top" style="vertical-align:top;">
                                @if (is_array($event)) @if(isset($event['place'])){{ $event['place'] }} @else @dd ($event) @endif @else {{ $event->locationText() }}@endif
                            </td>
                        </tr>
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
        hash = hash.replace('#', '#{{ $randomId }}_')+'_row';
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
        window.scrollTo(0,0);
    });

    if ((document.location.hash != '') && (document.location.hash != '#')) {
        t = document.location.hash;
        if (t.charAt(0) == '#') t = t.substr(1);
        t = '#{{ $randomId }}_'+t;
        $('#{{ $randomId }}_details_content').html($(t).html());
        $('#{{ $randomId }}_details').show();
        $('#{{ $randomId }}_table').hide();
    }
</script>
@endif
