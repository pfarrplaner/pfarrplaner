<html>
<body>
<h1>Gottesdienste für "{{ $highlight }}"</h1>
<b>Von {{ $start }} bis {{ $end }}</b>
<hr/>
<table>
    <thead></thead>
    <tbody>
    @foreach ($services as $service)
        <tr>
            <td width="10%">{{ $service->day->date->format('d.m.Y') }}</td>
            <td width="10%">{{ strftime('%H:%M', strtotime($service->time)) }} Uhr</td>
            <td width="20%">@if (!is_object($service->location)){{ $service->special_location }}@else{{$service->location->name}}@endif </td>
            <td width="20%"> P: {{ $service->pastor }} </td>
            <td width="20%"> O: {{ $service->organist}} </td>
            <td width="20%"> M: {{ $service->sacristan }} </td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
