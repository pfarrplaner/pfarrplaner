@component('mail::message')
Antrag genehmigt
================

Hallo {{ $absence->user->first_name }},

Der folgende Abwesenheitsantrag wurde am {{ \Carbon\Carbon::now()->formatLocalized('%A, %d.%m.%Y, um %H:%M Uhr') }} von
{{ $absence->approvedBy->name }} genehmigt.

@component('mail::panel')
**{{ $absence->reason }}**

{{ $absence->from->formatLocalized('%A, %d.%m.%Y') }} bis {{ $absence->to->formatLocalized('%A, %d.%m.%Y') }}
@endcomponent

@if($absence->approver_notes)
Der Genehmigung wurde folgende Notiz beigefügt:

@component('mail::panel')
{!! $absence->approver_notes !!}
@endcomponent

@endif

@endcomponent
