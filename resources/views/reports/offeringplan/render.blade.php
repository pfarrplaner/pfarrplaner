<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <title>Opferplan {{ $year }} für {{ $city->name }}</title>
    <style>
        body, * {
            font-family: helveticacondensed;
        }
        .table {
            border-collapse: collapse;
        }
        .table td {
            margin: 0;
            border: 0;
            padding: 3px;
        }
    </style>
</head>
<body>
<h1 style="margin-bottom: 0cm; padding-bottom: 0;">Opferplan {{ $year }} für {{ $city->name }}</h1>
<p style="font-size: 0.6em; color: gray; margin-top: 0;">Stand: {{strftime('%A, %d. %B %Y, %H:%M Uhr')}}</p>

<htmlpagefooter name="myFooter2">
    <div style="width: 100%; font-size: 8pt; text-align: right;">Seite {PAGENO}/{nbpg}</div>
</htmlpagefooter>
<sethtmlpagefooter name="myFooter2" value="on" />

@if($count)

    <table class="table table-fluid table-striped" cellspacing="0" border="0">
        <thead>
        <tr>
            <th>Gottesdienst</th>
            <th>Opferzweck</th>
            <th>Typ</th>
            <th>Anmerkungen</th>
            <th>Zähler 1</th>
            <th>Zähler 2</th>
        </tr>
        </thead>

        <tbody>
        <?php $ct = 0 ?>
        @foreach ($services as $dayServices)
            @foreach ($dayServices as $service)
                <?php $ct++; ?>
                <tr @if($ct %2 == 1)style="background-color: lightgray;"@endif>
                    <td valign="top" style="max-width: 3.5cm; width: 3.5cm;"><small><b>{{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText(true) }}
                            <br />{{ $service->locationText() }}</b>
                        @if ($service->descriptionText() != '')<br />{{ $service->descriptionText }}</small>@endif
                    </td>
                    <td valign="top">{{ $service->offering_goal }}</td>
                    <td valign="top" style="width: 1.3cm;"><small>@if($service->offering_type == 'PO')Pflicht @else @if($service->offering_type == 'eO')empf. @else eig.@endif @endif</small>
                    </td>
                    <td valign="top">{{ $service->offering_description }}</td>
                    <td valign="top"><small>{{ $service->offerings_counter1 }}</small></td>
                    <td valign="top"><small>{{ $service->offerings_counter2 }}</small></td>
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
    <br />

    @if($city->default_offering_goal)
        <p><b>Opferzweck, soweit nicht anders angegeben:</b><br /> {{ $city->default_offering_goal }} @if($city->default_offering_description)({{ $city->default_offering_description }}) @endif</p>
    @endif
    @if($city->default_funeral_offering_goal)
        <p><b>Opferzweck bei Beerdigungen (soweit nicht anders angegeben):</b><br /> {{ $city->default_funeral_offering_goal }} @if($city->default_funeral_offering_description)({{ $city->default_funeral_offering_description }}) @endif</p>
    @endif
    @if($city->default_wedding_offering_goal)
        <p><b>Opferzweck bei Trauungen (soweit nicht anders angegeben):</b><br /> {{ $city->default_wedding_offering_goal }} @if($city->default_wedding_offering_description)({{ $city->default_wedding_offering_description }}) @endif</p>
    @endif



@else
    <p>In dem angegebenen Zeitraum wurden keine Gottesdienste gefunden.</p>
@endif

</body>
</html>
