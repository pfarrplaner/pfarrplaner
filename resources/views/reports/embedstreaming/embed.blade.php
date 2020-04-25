@if((null === $nextService) && (null=== $lastService))
    <p>Zur Zeit sind keine Streaminggottesdienste eingetragen.</p>
@else
    <ul class="collapsible" data-collapsible="accordion" id="serviceCollapsible">
        @if($nextService !== null)
            <?php $liturgy = \App\Liturgy::getDayInfo($nextService->day) ?>
            <li class="active">
                <div class="collapsible-header">
                    <span>Gottesdienst am {{ $nextService->day->date->format('d.m.Y') }}</span></div>
                <div class="collapsible-body" style="">
                    <div class="row ctype-text listtype-none showmobdesk-0">
                        <div id="{{ uniqid() }}" class="col s12 default ">
                            <div
                                style="background-color: darkgray; position: relative; height: 0; padding-bottom: 56.25%;">
                                <iframe
                                    style="background-color: lightgray; position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                    src="https://www.youtube.com/embed/{{ \App\Helpers\YoutubeHelper::getCode($nextService->youtube_url) }}"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                            </div>
                            <table class="table serviceTable">
                                <tbody>
                                @if($nextService->songsheet)
                                    <tr>
                                        <td valign="top"><span class="fa fa-file fa-2x"></span></td>
                                        <td valign="top">
                                            Zu diesem Gottesdienst gibt es Texte und Lieder zum Herunterladen.
                                        </td>
                                        <td valign="top" class="text-right">
                                            <a class="btn btn-secondary"
                                               href="{{ route('storage', ['path' => pathinfo($nextService->songsheet, PATHINFO_FILENAME), 'prettyName' => $nextService->day->date->format('Ymd').'-Liedblatt.'.pathinfo($nextService->songsheet, PATHINFO_EXTENSION)]) }}">Liedblatt</a>
                                        </td>
                                    </tr>
                                @endif
                                @if($nextService->offering_goal)
                                    <tr>
                                        <td valign="top"><span class="fa fa-euro-sign fa-2x"></span></td>
                                        <td valign="top">
                                            Zu diesem Gottesdienst bitten wir um Spenden für folgenden
                                            Zweck: {{ $nextService->offering_goal }}
                                        </td>
                                        <td valign="top" class="text-right">
                                            @if($nextService->offerings_url)
                                                <a class="btn btn-secondary" href="{{ $nextService->offerings_url }}"
                                                   target="_blank">Spenden</a>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                                @if($nextService->cc_streaming_url)
                                    <tr>
                                        <td valign="top"><img src="{{ asset('img/cc.png') }}" width="30px"
                                                              style="margin-left: -4px;"/></td>
                                        <td valign="top">
                                            Auch ein Kindergottesdienst wird
                                            am {{ $nextService->day->date->format('d.m.Y') }}
                                            um @if($nextService->cc_alt_time != '') {{ substr($nextService->cc_alt_time,0,5) }} @else {{ $nextService->timeText(false) }} @endif
                                            Uhr live übertragen.
                                        </td>
                                        <td valign="top" class="text-right">
                                            <a class="btn btn-secondary" href="{{ $nextService->cc_streaming_url }}"
                                               target="_blank">Kinderkirche</a>
                                        </td>
                                    </tr>
                                @endif
                                @if($nextService->meeting_url)
                                    <tr>
                                        <td valign="top"><span class="fa fa-coffee fa-2x"></span></td>
                                        <td valign="top">
                                            Nach dem Gottesdienst laden wir online zum "virtuellen Kirchenkaffee" ein.
                                            Dort gibt es die Möglichkeit, miteinander ins Gespräch zu kommen.
                                            <br/><small>Auf dem Smartphone wird dazu evtl. die App "Microsoft Teams"
                                                benötigt.</small>
                                        </td>
                                        <td valign="top" class="text-right">
                                            <a class="btn btn-secondary" href="{{ $nextService->meeting_url }}"
                                               target="_blank">Kirchenkaffee</a>
                                        </td>
                                    </tr>
                                @endif
                                </tbody>
                            </table>


                        </div>
                    </div>
                </div>
            </li>
        @endif
        <?php $ctr = 0; ?>
        @foreach ($lastServices as $lastService)
            @if($ctr <= 10)
                @if (($lastService->youtube_url != '') || ($lastService->recording_url != ''))
                    <?php $ctr++; ?>
                    <?php $liturgy = \App\Liturgy::getDayInfo($lastService->day) ?>
                    <li @if(($loop->index == 1) && ($nextService === null))class="active" @endif>
                        <div class="collapsible-header">
                            <span>Gottesdienst vom {{ $lastService->day->date->format('d.m.Y') }} (Aufzeichnung)</span>
                        </div>
                        <div class="collapsible-body" style="">
                            <div class="row ctype-text listtype-none showmobdesk-0">
                                <div id="{{ uniqid() }}" class="col s12 default ">
                                    @if($lastService->youtube_url != '')
                                        <div
                                            style="background-color: darkgray; position: relative; height: 0; padding-bottom: 56.25%;">
                                            <iframe
                                                style="background-color: lightgray; position: absolute; top: 0; left: 0; width: 100%; height: 100%;"
                                                src="https://www.youtube.com/embed/{{ \App\Helpers\YoutubeHelper::getCode($lastService->youtube_url) }}"
                                                frameborder="0"
                                                allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture"
                                                allowfullscreen></iframe>
                                        </div>
                                    @endif
                                    <table class="table serviceTable">
                                        <tbody>
                                        @if($lastService->songsheet)
                                            <tr>
                                                <td valign="top"><span class="fa fa-file fa-2x"></span></td>
                                                <td valign="top">
                                                    Zu diesem Gottesdienst gibt es Texte und Lieder zum Herunterladen.
                                                </td>
                                                <td valign="top" class="text-right">
                                                    <a class="btn btn-secondary"
                                                       href="{{ route('storage', ['path' => pathinfo($lastService->songsheet, PATHINFO_FILENAME), 'prettyName' => $lastService->day->date->format('Ymd').'-Liedblatt.'.pathinfo($lastService->songsheet, PATHINFO_EXTENSION)]) }}">Liedblatt</a>
                                                </td>
                                            </tr>
                                        @endif
                                        @if($lastService->offering_goal)
                                            <tr>
                                                <td valign="top"><span class="fa fa-euro-sign fa-2x"></span></td>
                                                <td valign="top">
                                                    Zu diesem Gottesdienst bitten wir um Spenden für folgenden
                                                    Zweck: {{ $lastService->offering_goal }}
                                                </td>
                                                <td valign="top" class="text-right">
                                                    @if($lastService->offerings_url)
                                                        <a class="btn btn-secondary"
                                                           href="{{ $lastService->offerings_url }}"
                                                           target="_blank">Spenden</a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endif
                                        @if($lastService->recording_url)
                                            <tr>
                                                <td valign="top" colspan="3">
                                                    Zu diesem Gottesdienst gibt es eine Audioaufzeichnung:<br/>
                                                    <figure class="width100">
                                                        <audio controls="">
                                                            <source src="{{ $lastService->recording_url }}">
                                                        </audio>
                                                        <figcaption class="fontcol">
                                                            <p class="description"><em>Predigt
                                                                    vom {{ $lastService->day->date->format('d.m.Y') }}
                                                                    , {{ $lastService->participantsText('P') }}</em></p>
                                                        </figcaption>
                                                    </figure>
                                                </td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>


                                </div>
                            </div>
                        </div>
                    </li>
                @endif
            @endif
        @endforeach
    </ul>

    <script>
        $('head').append('<link rel="stylesheet" type="text/css" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css">');

        var list = $('#serviceCollapsible').collapsible('open', 0);
        $('#serviceCollapsible li:first').addClass('active');
        $('#serviceCollapsible div:first').addClass('active');
        $('#serviceCollapsible li:first div.collapsible-body:first').show();
    </script>
@endif
