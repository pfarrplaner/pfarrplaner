@if(count($services))
    @if($title) <h3>{{ $title }}</h3> @endif
    <div id="{{ $id }}">
        <template id="buttonstyles">
            .service-list-table td {
            vertical-align: top;
            }
            .service-list-table tr.youtube {
            cursor: pointer;
            }
            .service-list-table tr:hover td {
            background-color: #eee;
            }
            .youtube-button {
            display: inline-block;
            margin-top: 1px; margin-bottom: 3px;
            border: solid 1px darkgray; background-color: white; padding: 7px; border-radius: 3px;
            }
            .youtube-button:hover {
            background-color: #dddddd;
            }
            .service-list-table .small-button {
            font-size: .8em;
            border: solid 1px darkgray;
            padding: 4px 7px;
            border-radius: 3px;
            color: black;
            }
            .fa-info-circle {
                color: gray;
            }
            .fa-info-circle:hover {
                color: black;
            }
            .liturgical-day {
                margin: 0;
                padding: 0;
                font-style: italic;
            }
        </template>
        <table class="ce-table service-list-table ">
            <thead>
            <th>Datum</th>
            <th>Uhrzeit</th>
            @if (!isset($locationIds) || count($locationIds) >1)
                <th>Ort</th>
            @endif
            <th></th>
            <th>Liturg*in</th>
            </thead>
            <tbody>
            <?php $lastDate = ''; ?>
            @foreach($services as $service)
                <tr data-url="{{ $service->youtube_url ?? '' }}" class="{{ $service->youtube_url ? 'youtube' : '' }}"
                    title="{{ $service->youtube_url ? 'Klicken Sie hier, um den Gottesdienst auf Youtube anzuschauen' : '' }}">
                    @if ($lastDate != $service->day->date->format('d.m.') )
                        <td>{{ $lastDate = $service->day->date->format('d.m.') }}</td>
                    @else
                        <td></td>
                    @endif
                    <td>{{ $service->timeText() }}</td>
                    @if (!isset($locationIds) || count($locationIds) >1)
                        <td>{{ $service->locationText() }}</td>
                    @endif
                    <td>
                        @if($service->liturgicalInfo)
                            <div class="liturgical-day">{{ $service->liturgicalInfo['title'] }}
                                <span class="fas fa-info-circle" title="{{ $service->liturgicalInfo['title'] }}: {{ $service->liturgicalInfo['litProfileGist'] }}&#10;&#10;Klicken Sie, um weitere Information zu diesem Tag zu bekommen."
                                      data-location="https://www.kirchenjahr-evangelisch.de/article.php#{{ $service->liturgicalInfo['dayId'] }}"></span>
                            </div>
                        @endif
                        {{ $service->descriptionText() }}
                        @if($service->controlled_access)
                            @component('components.service.controlledAccess', ['service' => $service]) @endcomponent
                        @endif
                        @if (is_object($service->location) && ($service->location->instructions != ''))@if ($service->descriptionText()!='')
                            <br/>@endif
                        {!! nl2br($service->location->instructions) !!}<br/>
                        @endif
                            @if ($service->youtube_url)
                                @if ($service->descriptionText()!='')<br/>@endif
                                <img src="{{ asset('img/brands/youtube.png') }}" height="10px"/> Klicken Sie hier, um
                                den Gottesdienst auf YouTube anzuschauen
                            @endif
                        <br />
                        @if ($service->songsheet) <span class="small-button" href="{{ $service->songsheetUrl }}" title="Klicken Sie hier, um das Liedblatt herunterzuladen"><span class="fa fa-file-pdf"></span> Liedblatt</span> @endif
                        @if ($service->offerings_url) <span class="small-button" href="{{ $service->offerings_url }}" title="Klicken Sie hier, um online zu spenden"><span class="fa fa-coins"></span> Opfer</span> @endif
                        @if ($service->cc_streaming_url) <span class="small-button" href="{{ $service->cc_streaming_url }}"title="Klicken Sie hier, um den Kindergottesdienst auf YouTube anzuschauen"><img src="{{ asset('img/cc.png') }}" height="12px"> Kinderkirche</span> @endif
                        @if ($service->external_url) <span class="small-button" href="{{ $service->external_url }}"  title="Klicken Sie hier, um zur Predigtseite zu gelangen"><span class="fa fa-globe"></span> Externe Seite zur Predigt</span> @endif
                    </td>
                    <td>{{ $service->participantsText('P') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <script>
        $('<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" />').appendTo("head");
        $("<style type='text/css'>" + $('#buttonstyles').html() + "</style>").appendTo("head");
        $('.service-list-table tr').click(function (e) {
            e.preventDefault();
            if ($(this).data('url')) window.open($(this).data('url'));
        });
        $('.service-list-table span.small-button').click(function (e) {
            e.preventDefault();
            e.stopPropagation();
            if ($(this).attr('href')) window.open($(this).attr('href'));
        });
        $('.fa-info-circle').click(function (e) {
            e.stopPropagation();
            e.preventDefault();
            if ($(this).data('location')) window.open($(this).data('location'));
        });
    </script>
@endif
