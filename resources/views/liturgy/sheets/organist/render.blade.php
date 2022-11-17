<!DOCTYPE html>
<html lang="de">
<head>
    <style>
        @font-face {
            font-face-name: 'Helvetica Condensed';
        }

        @page {
            odd-footer-name: html_PageFooter;
            even-footer-name: html_PageFooter;
        }


        body {
            line-height: 1.1em;
            font-size: 11pt;
            font-family: 'Helvetica Condensed', sans-serif;
        }

        h1 {
            font-size: 16pt;
            color: #951981;
        }

        h2 {
            font-size: 14pt;
        }

        h3 {
            font-size: 13pt;
            color: #951981;
        }

        h1, h2, h3 {
            margin: 0;
            padding: 0;
            line-height: 1.4em;
            font-weight: bold;
        }

        table.head {
            width: 100%;
        }

        table.table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 1cm;
        }

        table.table td, .table.table th {
            margin: 0;
            padding: 3px;
            border: solid 1px gray;
        }

        table.table tr:first td, table.table tr:first th {
            border-top-width: 0;
        }

        table.table td.section-title {
            border-left-color: white;
            border-right-color: white;
            padding-top: 0.5cm;
            padding-bottom: 0.2cm;
        }

        table.table td, th {
        }

        table.table tr:last-child td, tr:last-child th {
            border: 0;
            margin-bottom: 0;
        }

        table.table th {
            text-align: left;
        }

        table.table td h3 {
            text-align: left;
        }

        table.table .subtitle {
            font-weight: normal;
        }

        hr {
            border: 0;
            height: 1px;
            background: #333;
            background-image: linear-gradient(to right, #ccc, #333, #ccc);
        }

        ul li {
            list-style-type: square;
        }

        div.column {
            margin: 0;
            min-width: 7.5cm;
            max-width: 7.5cm;
            width: 7.5cm;
            float: left;
            padding: 1cm;
            padding-bottom: 0;
            min-height: 19.4cm;
            height: 19.4cm;
        }

        div.column1 {
            margin: 0;
            width: 9.5cm;
            float: left;
            padding: 0;
            height: 20.4cm;
        }

        .column3 li {
            line-height: 1.5em;
        }


        .titlecontainer {
            width: 100%;
        }

        .title {
            width: 100%;
            margin: 0;
            text-align: left;
        }

        .subtitle {
            width: 100%;
            margin: 0;
            text-align: left;
        }

        figcaption.image-copyrights, figcaption.image-copyrights a {
            text-decoration: none;
            font-size: 7pt;
            color: darkgray;
        }


        img.logo {
            width: 4cm;
            float: right;
            border-left: solid 3px #951981;
            padding: 0.5cm 0 0 1px;
        }

        img.logo-bottom {
            width: 3cm;
            padding: 0 0.5cm 0.5cm 0.5cm;
        }

        div.column1-container {
            clear: both;
            height: 12.8cm;
            padding: 1cm 0.7cm 1cm 1cm;
        }

        figure {
            margin: 0;
        }

        .column1-container figure {
            margin-top: 1cm;
        }

        .column1-container img {
            max-width: 100%;
            max-height: 100%;
        }

        .date-and-preacher {
        }

        .small {
            color: darkgray;
            font-size: 10pt;
        }

        .address {
            text-align: center;
            font-size: 9pt;
        }

        .address img {
            max-width: 4cm;
        }

        ul.liturgy li {
            padding: 0;
            margin: 0;
        }

        table.table tr.first td {
            border-top: 0;
        }

        table.footer {
            font-size: 0.6em;
            width: 100%;
            padding: 0;
            border: 0;
            margin: 0;
        }

        table.footer td {
            padding: 0;
            border: 0;
            margin: 0;
            width: 33%;
            vertical-align: top;
        }
    </style>
</head>
<body>
<htmlpagefooter name="PageFooter">
    <table class="footer" style="">
        <tr>
            <td valign="top">Stand: {{ \Carbon\Carbon::now()->setTimezone('Europe/Berlin')->format('d.m.Y, H:i') }}
                Uhr
            </td>
            <td style="text-align: center;">@auth{{ Auth::user()->fullName(false) }}@endauth</td>
            <td valign="top" style="text-align: right">Seite {PAGENO} / {nbpg}</td>
        </tr>
    </table>
</htmlpagefooter>
<table class="head" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top">
            <h1>{{ $service->titleText(false) }}</h1>
        </td>
        <td valign="top" style="font-size: 8pt; text-align: right;">
            {{ $service->date->format('d.m.Y') }}, {{ $service->timeText() }}, {{ $service->locationText() }} <br/>
        </td>
    </tr>

</table>
<table class="table" border="0">
    <tbody>
    @foreach($service->liturgyBlocks as $block)
        <tr @if($loop->first) class="first" @endif>
            <td class="section-title" colspan="3" valign="top"><h3>{{ $block->title }}</h3></td>
        </tr>
        @foreach ($block->items as $item)
            @php
                if (isset($item->data['responsible'])) {
                    $concernsOrganists = in_array('ministry:organists', $item->data['responsible']);
                    if (!$concernsOrganists) {
                        foreach ($service->organists as $organist) {
                            $concernsOrganists = $concernsOrganists || in_array('user:'.$organist->id, $item->data['responsible']);
                        }
                    }
                } else {
                    $concernsOrganists = false;
                }
            @endphp
            <tr>
                <td valign="top" width="25%"
                    style="font-weight: {{ $concernsOrganists ? 'bold' : 'normal' }}">{{ $item->title }}</td>
                @if($concernsOrganists && $item->data_type == 'freetext')
                    <td valign="top"
                        colspan="4">@if($item->data['description']){{ (false !== strpos($item->data['description'], "\n")) ? explode("\n", $item->data['description'])[0] : substr($item->data['description'], 0, 80).'...' }}@endif</td>
                @elseif($concernsOrganists && $item->data_type == 'liturgic')
                    <td valign="top" colspan="4"></td>
                @elseif($item->data_type == 'song')
                    @php $helper = new App\Liturgy\ItemHelpers\SongItemHelper($item); @endphp

                    @if(isset($item->data['song']) && isset($item->data['song']['song']))
                        <td valign="top" style="font-weight: bold">
                            @if (isset($item->data['song']['code']) || isset($item->data['song']['songbook']))
                                {{ $item->data['song']['code'] ?? ($item['song']['songbook']['name'] ?? '') }}
                            @endif
                            {{ $item->data['song']['reference'] ?? '' }}@if(isset($item->data['song']) && isset($item->data['song']['altEG']))
                                (EG {{ $item->data['song']['altEG'] }})@endif
                        </td>
                        <td valign="top" style="font-weight: bold">{{ $helper->forceVerseString() }}</td>
                        <td valign="top" style="font-weight: bold">{{ $item->data['song']['song']['title'] }}</td>
                        <td valign="top" style="font-weight: bold">{{ $helper->getActiveVerseCount(true, true) }}</td>
                    @else
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    @endif
                @elseif($item->data_type == 'psalm')
                    @if(isset($item->data['psalm']))
                        <td valign="top">
                        @php $helper = new App\Liturgy\ItemHelpers\PsalmItemHelper($item); @endphp
                        @if (isset($item->data['psalm']['songbook_abbreviation']) || isset($item->data['psalm']['songbook']))
                            {{ $item->data['psalm']['songbook_abbreviation'] ?? ($item['psalm']['songbook'] ?? '') }}
                        @endif
                            {{ $item->data['psalm']['reference'] ?? '' }}
                        </td>
                        <td valign="top">{{ str_replace('Psalm', 'Ps.', $item->data['psalm']['title'] ?? '') }}</td>
                        <td valign="top" colspan="2 ">
                            <div style="font-size: .9em">
                                [...]<br />
                            {!! nl2br(str_replace("\t", '&nbsp;&nbsp;&nbsp;&nbsp;', $helper->getFinalVerses())) !!}
                            </div>
                        </td>
                    @else
                        <td valign="top" colspan="4"></td>
                    @endif
                @elseif($concernsOrganists && $item->data_type == 'reading')
                    <td valign="top" colspan="4">{{ $item->data['reference'] }}</td>
                @elseif($concernsOrganists && $item->data_type == 'sermon')
                    @if($service->sermon)
                        <td valign="top"
                            colspan="4">@if($service->sermon->title){{ $service->sermon->fullTitle }} @if($service->sermon->reference)
                                ({{ $service->sermon->reference }}
                                )@endif @else {{ $service->sermon->reference }} @endif</td>
                    @else
                        <td></td>
                    @endif
                @else
                    <td colspan="4"></td>
                @endif
            </tr>
        @endforeach
    @endforeach
    </tbody>
</table>

