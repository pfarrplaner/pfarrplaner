@component('mail::message')
Abwesenheitsantrag: Bitte überprüfen
====================================

{{ $absence->user->name }} bittet um Überprüfung eines Abwesenheitsantrags:

@component('mail::panel')
**{{ $absence->reason }}**

{{ $absence->from->formatLocalized('%A, %d.%m.%Y') }} bis {{ $absence->to->formatLocalized('%A, %d.%m.%Y') }}
@endcomponent

@component('mail::button', ['url' => route('absence.edit', $absence->id)])
    Antrag prüfen
@endcomponent


@endcomponent
