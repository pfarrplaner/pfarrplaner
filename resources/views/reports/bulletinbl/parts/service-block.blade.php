@if (isset($serviceList[$location->id][$day->date->format('Y-m-d')]))
    <?php $service = $serviceList[$location->id][$day->date->format('Y-m-d')]->first() ?>
    @if(is_object($service))
        {{ $service->participantsText('P', false, false, '|') }}
        @if(is_object($service->location)) @if ($service->time != $service->location->default_time)
            <b>{{ $service->timeText(true, '.') }}</b>
        @else
            @include('reports.bulletinbl.parts.service-logos')
        @endif @endif
        @if($service->description)<br/>{{ $service->description }}
        @endif
        @if(is_object($service->location)) @if ($service->time != $service->location->default_time)
            @include('reports.bulletinbl.parts.service-logos')
        @endif
        @else
            @include('reports.bulletinbl.parts.service-logos')
        @endif
    @else
        @if ($empty[$location->id][$day->id]){!! nl2br($empty[$location->id][$day->id]) !!} @endif
    @endif
@endif
