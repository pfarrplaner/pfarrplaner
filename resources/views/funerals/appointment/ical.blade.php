BEGIN:VCALENDAR
VERSION:2.0
PRODID:{{ env('APP_URL') }}
METHOD:PUBLISH
BEGIN:VEVENT
UID:FUNERAL-{{ $funeral->id }}{{ '@' }}{{ parse_url(env('APP_URL'), PHP_URL_HOST) }}
LOCATION:{{ $funeral->relative_address }}, {{ $funeral->relative_zip }} {{ $funeral->relative_city }}
SUMMARY:Trauergespräch {{ $funeral->buried_name }}
DESCRIPTION:{{ $funeral->type }} am {{ $service->day->date->format('d.m.Y') }} um {{ $service->timeText() }}, {{ $service->locationText() }} \n\n @if($funeral->relative_contact_data)-- Kontakt:\n {{ strtr($funeral->relative_contact_data, ["\n" => '\n', "\r" => '']) }}\n @endif \n\n -- Pfarrplaner:\nLink zum Gottesdienst: {{ route('service.edit', $service) }}\nLink zum Taufeintrag: {{ route('funerals.edit', $funeral) }}
CLASS:PUBLIC
DTSTART:{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $funeral->appointment->format('Y-m-d H:i').':00', 'Europe/Berlin')->setTimezone('UTC')->format('Ymd\THis\Z') }}
DTEND:{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $funeral->appointment->addHour(1)->format('Y-m-d H:i').':00', 'Europe/Berlin')->setTimezone('UTC')->format('Ymd\THis\Z') }}
DTSTAMP:{{ $funeral->updated_at->setTimezone('UTC')->format('Ymd\THis\Z') }}
END:VEVENT
END:VCALENDAR
