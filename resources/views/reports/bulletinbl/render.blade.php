<!DOCTYPE html>
<html lang="de">
<head>
    <title>Tabelle fuer den Balinger Gemeindebrief</title>

    @include('reports.bulletinbl.parts.css')

</head>
<body>

<!-- Seite 1 -->
<div class="page-container">
    <div class="content-container">
        <div class="inner">

        <h1>Gesamtkirchengemeinde: <span class="highlight">Gottesdienste</span></h1>
        <table class="services">
            <thead>
            <tr>
                <th colspan="5" class="line" style="background-color: white"></th>
            </tr>
            <tr>
                <th>Datum</th>
                @foreach($locations as $location)
                    @if($loop->index < 2)
                        <th class="column-spacer" style="height: 11mm;"></th>
                        <th class="">{{ $location->name }},
                            @if (strlen($location->name)<27) <br /> @endif
                            {{ \App\Tools\StringTool::timeString($location->default_time, true, '.') }}</th>
                    @endif
                @endforeach
            </tr>
            <tr>
                <th colspan="5" class="line" style="background-color: white"></th>
            </tr>
            </thead>
            <tbody>
            @foreach ($days as $day)
                <tr class="@if($loop->index % 2) highlight-row @endif">
                    <td>
                       <b>{{ \Carbon\Carbon::parse($day)->formatLocalized('%d. %B') }},<br/>
                        {{ str_replace('So. n.', 'Sonntag nach', $day->name) }}</b>
                    </td>
                    @foreach($locations as $location)
                        @if($loop->index < 2)
                            <th class="column-spacer"></th>
                            <td class="">
                                @include('reports.bulletinbl.parts.service-block')
                            </td>
                        @endif
                    @endforeach
                </tr>
                <tr>
                    <th class="line"></th>
                    @foreach($locations as $location)
                        @if($loop->index < 2)
                            <th class="column-spacer" style="height: 1mm;"></th>
                            <td class="line"></td>
                        @endif
                    @endforeach

                </tr>
            @endforeach
            </tbody>
        </table>
        <table class="icon-info">
            <thead>
            <tr>
                <td>
                    <img src="{{ resource_path('img/bulletinbl/taufe.png') }}" height="4mm" />
                </td>
                <td>
                    <img src="{{ resource_path('img/bulletinbl/familien-gd.png') }}" height="4mm" />
                </td>
                <td>
                    <img src="{{ resource_path('img/bulletinbl/kirchenkaffee.png') }}" height="4mm" />
                </td>
                <td>
                    <img src="{{ resource_path('img/bulletinbl/musik.png') }}" height="4mm" />
                </td>
                <td>
                    <img src="{{ resource_path('img/bulletinbl/abendmahl.png') }}" height="4mm" />
                </td>
                <td>
                    <img src="{{ resource_path('img/bulletinbl/posaunenchor.png') }}" height="4mm" />
                </td>
                <td>
                    <img src="{{ resource_path('img/bulletinbl/fahrdienst.png') }}" height="4mm" />
                </td>
                <td>
                    <img src="{{ resource_path('img/bulletinbl/info.png') }}" height="4mm" />
                </td>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>
                    Tauf-<br />sonntag
                </td>
                <td>
                    Familien-<br />GD
                </td>
                <td>
                    Kirchen-<br />kaffee
                </td>
                <td>
                    Musik<br /> im GD
                </td>
                <td>
                    Abend-<br />mahl
                </td>
                <td>
                    mit Posau-<br />nenchor
                </td>
                <td>
                    Fahrdienst,<br />15 Min. vor<br />Gottesdienst
                </td>
                <td>
                    weitere Infos<br /> im Gemein-<br />debrief
                </td>
            </tr>
            </tbody>
        </table>

        </div>
    </div>
    <div class="footer-container">
        <div class="page-footer">{{ $pageno }}</div>
    </div>
</div>

<!-- Seite 2 -->

<div class="page-container">
    <div class="content-container">
        <div style="width: 100%; text-align:right; max-height: 10mm; padding-bottom: 2mm; padding-top: 1mm; padding-right: 1mm; float: left;">
            <img src="{{ resource_path('img/bulletinbl/ev3-logo.png') }}" height="8mm" />
        </div>
        <div style="margin: 0; padding: 0; width: 103mm; height: 180mm; overflow: hidden; border: 0; float: left;">
            <table class="services">
                <thead>
                <tr>
                    <th colspan="3" class="line" style="background-color: white"></th>
                </tr>
                <tr>
                    @foreach($locations as $location)
                        @if($loop->index >= 2)
                            <th class="">{{ $location->name }},
                                @if (strlen($location->name)<27) <br /> @endif
                                {{ \App\Tools\StringTool::timeString($location->default_time, true, '.') }}</th>
                            @if ($loop->index == 2)<th class="column-spacer" style="height: 11mm;"></th>@endif
                        @endif
                    @endforeach
                </tr>
                <tr>
                    <th colspan="3" class="line" style="background-color: white"></th>
                </tr>
                </thead>
                <tbody>
                @foreach ($days as $day)
                    <tr class="@if($loop->index % 2) highlight-row @endif">
                        @foreach($locations as $location)
                            @if($loop->index >= 2)
                                <td class="">
                                    @include('reports.bulletinbl.parts.service-block')
                                </td>
                                @if ($loop->index == 2)<td class="column-spacer" style="height: 11mm;"></td>@endif
                            @endif
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($locations as $location)
                            @if($loop->index >= 2)
                                <td class="line"></td>
                                @if($loop->index == 2)<td class="column-spacer" style="height: 1mm;"></td>@endif
                            @endif
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <div style="margin: 0; margin-left: -1.5mm; padding: 0; width: 53mm; height: 180mm; overflow: hidden; border: 0; float: left;">
            <table class="services large" width="53mm">
                <thead>
                <tr>
                    <th colspan="2" class="line" style="background-color: white"></th>
                </tr>
                <tr>
                    <th class="column-spacer" style="height: 11mm;"></th>
                    <th style="height: 11mm;">Besondere<br />Gottesdienste</th>
                </tr>
                <tr>
                    <th colspan="2" class="line" style="background-color: white"></th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <div class="infobox">
                @foreach ($specialServices as $group)
                    <p style="font-weight: bold;">{{ $group['options']['group'] }}@if ($group['options']['sameTime'] && (count($group['services']) > 1)),
                            jeweils {{ $group['options']['time'] }}
                            @endif
                    </p>
                    <ul>
                    @foreach($group['services'] as $service)
                            <li>
                                {{ $service->date->formatLocalized('%A, %d. %B') }}{!! ((!$group['options']['sameTime']) || (count($group['services']) == 1)) ? ', '.$service->timeText(true, '.', false, true) : '' !!}{{ ((!$group['options']['sameLocation']) && (trim($service->locationText()) != '')) ? ', '.$service->locationText() : ''  }}
                            </li>
                    @endforeach
                    </ul>
                @endforeach
            </div>
        </div>
    </div>
    <div class="footer-container">
        <div class="page-footer" style="text-align: right;">{{ $pageno+1 }}</div>
    </div>
</div>

</body>
</html>
