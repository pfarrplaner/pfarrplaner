BEGIN:VCALENDAR
VERSION:2.0
PRODID:{{ env('APP_URL') }}
METHOD:PUBLISH
BEGIN:VEVENT
UID:BAPTISM-{{ $baptism->id }}{{ '@' }}{{ parse_url(env('APP_URL'), PHP_URL_HOST) }}
LOCATION:{{ $baptism->candidate_address }}, {{ $baptism->candidate_zip }} {{ $baptism->candidate_city }}
SUMMARY:Taufgespräch {{ $baptism->candidate_name }}
DESCRIPTION:Taufe am {{ $service->day->date->format('d.m.Y') }} um {{ $service->timeText() }}, {{ $service->locationText() }} \n\n -- Kontakt:\n @if($baptism->candidate_phone)Telefon: {{ $baptism->candidate_phone }}\n @endif @if($baptism->candidate_email)E-Mail: {{ $baptism->candidate_email }}\n @endif \n\n -- Pfarrplaner:\nLink zum Gottesdienst: {{ route('services.edit', $service) }}\nLink zum Taufeintrag: {{ route('baptisms.edit', $baptism) }}
CLASS:PUBLIC
DTSTART:{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $baptism->appointment->format('Y-m-d H:i').':00', 'Europe/Berlin')->setTimezone('UTC')->format('Ymd\THis\Z') }}
DTEND:{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $baptism->appointment->addHour(1)->format('Y-m-d H:i').':00', 'Europe/Berlin')->setTimezone('UTC')->format('Ymd\THis\Z') }}
DTSTAMP:{{ $baptism->updated_at->setTimezone('UTC')->format('Ymd\THis\Z') }}
END:VEVENT
END:VCALENDAR
