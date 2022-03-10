BEGIN:VCALENDAR
VERSION:2.0
PRODID:{{ $calendarLink->getLink() }}
METHOD:PUBLISH
@foreach($data as $service)@if(is_object($service))@if(is_object($service->day))BEGIN:VEVENT
UID:{{ $service->id }}{{ '@' }}{{ parse_url(env('APP_URL'), PHP_URL_HOST) }}
LOCATION:{{ $service->locationText() }}
SUMMARY:{{ wordwrap($service->titleText().' P: '.$service->participantsText('P').' O: '.$service->participantsText('O').' M: '.$service->participantsText('M').($service->description ? ' ('.$service->description.')' : ''), 64, "\r\n  ") }}
DESCRIPTION: {{ wordwrap ($service->descriptionText(), 62, "\r\n  ") }}
CLASS:PUBLIC
DTSTART:{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $service->date->format('Y-m-d').' '.$service->timeText(false).':00', 'Europe/Berlin')->setTimezone('UTC')->format('Ymd\THis\Z') }}
DTEND:{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $service->date->format('Y-m-d').' '.$service->timeText(false).':00', 'Europe/Berlin')->addHour(1)->setTimezone('UTC')->format('Ymd\THis\Z') }}
DTSTAMP:{{ $service->updated_at->setTimezone('UTC')->format('Ymd\THis\Z') }}
END:VEVENT
@endif @endif
@endforeach
END:VCALENDAR
