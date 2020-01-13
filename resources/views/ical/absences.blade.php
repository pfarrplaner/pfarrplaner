BEGIN:VCALENDAR
VERSION:2.0
PRODID:{{ route('ical.absences', ['userId' => $user->id, 'token' => $token]) }}

METHOD:PUBLISH
@foreach($absences as $absence)BEGIN:VEVENT
UID:{{ $absence->id }}{{ '@' }}absences.{{ parse_url(env('APP_URL'), PHP_URL_HOST) }}
SUMMARY: {{ $absence->fullDescription() }}
CLASS:PUBLIC
DTSTART;VALUE=DATE:{{ $absence->from->format('Ymd') }}
DTEND;VALUE=DATE:{{ $absence->to->copy()->addDay(1)->fochrrmat('Ymd') }}
DTSTAMP:{{ $absence->updated_at->setTimezone('UTC')->format('Ymd\THis\Z') }}
END:VEVENT
@endforeach
END:VCALENDAR
