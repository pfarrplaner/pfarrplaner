@component('mail::message')
Urlaubsantrag: Bitte überprüfen
===============================

{{ $absence->user->name }} bittet um Überprüfung eines Urlaubsantrags:

@component('mail::panel')
**{{ $absence->reason }}**

{{ $absence->from->formatLocalized('%A, %d.%m.%Y') }} bis {{ $absence->from->formatLocalized('%A, %d.%m.%Y') }}
@endcomponent

@component('mail::button', ['url' => route('absences.edit', $absence->id)])
    Urlaubsantrag prüfen
@endcomponent


@endcomponent
