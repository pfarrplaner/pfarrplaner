@component('mail::message')
Abwesenheitsantrag: Bitte um Genehmigung
========================================

{{ $absence->user->name }} bittet um Genehmigung eines Abwesenheitsantrags:

@component('mail::panel')
**{{ $absence->reason }}**

{{ $absence->from->formatLocalized('%A, %d.%m.%Y') }} bis {{ $absence->to->formatLocalized('%A, %d.%m.%Y') }}
@endcomponent

Der Antrag wurde am {{ \Carbon\Carbon::now()->formatLocalized('%A, %d.%m.%Y, um %H:%M Uhr') }} von
{{ $absence->checkedBy->name }} überprüft und zur Genehmigung weitergeleitet.

@component('mail::button', ['url' => route('absence.edit', $absence->id)])
    Antrag im Pfarrplaner öffnen
@endcomponent

@if($absence->admin_notes)
Der Überprüfung wurde folgende Notiz beigefügt:

@component('mail::panel')
    {!! $absence->admin_notes !!}
@endcomponent

@endif

@endcomponent
