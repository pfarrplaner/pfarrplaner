@component('mail::message')
Urlaubsantrag: Bitte um Genehmigung
===================================

{{ $absence->user->name }} bittet um Genehmigung eines Urlaubsantrags:

@component('mail::panel')
**{{ $absence->reason }}**

{{ $absence->from->formatLocalized('%A, %d.%m.%Y') }} bis {{ $absence->from->formatLocalized('%A, %d.%m.%Y') }}
@endcomponent

Der Antrag wurde am {{ \Carbon\Carbon::now()->formatLocalized('%A, %d.%m.%Y, um %H:%M Uhr') }} von
{{ $absence->checkedBy->name }} überprüft und zur Genehmigung weitergeleitet.

@component('mail::button', ['url' => route('absence.edit', $absence->id)])
    Urlaubsantrag im Pfarrplaner öffnen
@endcomponent

@if($absence->admin_notes)
Der Überprüfung wurde folgende Notiz beigefügt:

@component('mail::panel')
    {!! $absence->admin_notes !!}
@endcomponent

@endif

@endcomponent
