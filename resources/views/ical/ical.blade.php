BEGIN:VCALENDAR
VERSION:2.0
PRODID:{{ env('APP_URL') }}@if(null !== $token)ical/{{ $action }}/{{ $key }}/{{ $token }}@endif
METHOD:PUBLISH
@foreach($services as $service)BEGIN:VEVENT
UID:{{ $service->id }}{{ '@' }}{{ parse_url(env('APP_URL'), PHP_URL_HOST) }}
LOCATION:{{ $service->locationText() }}
SUMMARY:{{ wordwrap($service->titleText().' P: '.$service->participantsText('P').' O: '.$service->participantsText('O').' M: '.$service->participantsText('M').($service->description ? ' ('.$service->description.')' : ''), 64, "\r\n  ") }}
@if($service->description)
DESCRIPTION: {{ wordwrap ($service->description, 62, "\r\n  ") }}
@endif

CLASS:PUBLIC
DTSTART:{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $service->day->date->format('Y-m-d').' '.$service->timeText(false).':00', 'Europe/Berlin')->setTimezone('UTC')->format('Ymd\THis\Z') }}
DTEND:{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $service->day->date->format('Y-m-d').' '.$service->timeText(false).':00', 'Europe/Berlin')->addHour(1)->setTimezone('UTC')->format('Ymd\THis\Z') }}
DTSTAMP:{{ $service->updated_at->setTimezone('UTC')->format('Ymd\THis\Z') }}
END:VEVENT
@endforeach
END:VCALENDAR
