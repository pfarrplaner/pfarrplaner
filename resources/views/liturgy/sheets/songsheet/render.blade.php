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

        div.tab-spacer {
            display: inline-block;
            height: 2px;
            border: solid 1px red;
            min-width: 12.5mm;
        }
    </style>
</head>
<body>
<htmlpagefooter name="PageFooter">
    <table class="footer" style="">
        <tr>
            <td valign="top" style="text-align: right">Seite {PAGENO} / {nbpg}</td>
        </tr>
    </table>
</htmlpagefooter>
<h1>Lieder und Texte zum Gottesdienst am {{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText() }}, {{ $service->locationText() }}</h1>
@foreach($service->liturgyBlocks as $block)
    @foreach ($block->items as $item)
        @if($item->data_type == 'song')
            @if(isset($item->data['song']))
                @if($item->data['song']['title'])<h2>{{ $item->data['song']['title'] }}</h2>@endif
                <small> {{ $item->data['song']['songbook_abbreviation'] ?: ($item['song']['songbook'] ?: '') }}
                    {{ $item->data['song']['reference'] ?: '' }}@if ($item->data['verses']), {{ $item->data['verses'] }}</small>@endif

                @php $refrain = false; @endphp

                @foreach ($item->getHelper()->getActiveVerses() as $verse)
                    @if(($verse['refrain_before']) && (!$refrain))
                        @php $refrain = true; @endphp
                        <p style="font-style: italic;">Kehrvers: <br />{{ $item->data['song']['refrain'] }}</p>
                    @elseif ($verse['refrain_before'])
                        <p style="font-style: italic;">Kehrvers</p>
                    @endif
                    <p>{{ $verse['number'] }}. {{ $verse['text'] }}</p>
                    @if(($verse['refrain_after']) && (!$refrain))
                        @php $refrain = true; @endphp
                        <p style="font-style: italic;">Kehrvers: <br />{{ $item->data['song']['refrain'] }}</p>
                    @elseif ($verse['refrain_after'])
                        <p style="font-style: italic;">Kehrvers</p>
                    @endif
                @endforeach
        @endif
        @elseif($item->data_type == 'psalm')
                @if(isset($item->data['psalm']))
                    <h2>{{ $item->data['psalm']['songbook_abbreviation'] ?: ($item['psalm']['songbook'] ?: '') }}
                    {{ $item->data['psalm']['reference'] ?: '' }}
                    {{ $item->data['psalm']['title'] ?: '' }}</h2>
                    <p>{!! str_replace("@@@", "<br />", str_replace("\n", '<br /><br />', str_replace("\n\t", '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;', $item->data['psalm']['text']))) !!}</p>
                @endif
        @endif
    @endforeach
@endforeach
<p style="margin-top: 0.5cm;"><small>
        Stand: {{ \Carbon\Carbon::now()->setTimezone('Europe/Berlin')->format('d.m.Y, H:i') }} Uhr
    </small></p>
</body>
</html>
