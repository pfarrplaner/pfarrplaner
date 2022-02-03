@component('mail::message')
Urlaubsantrag abgelehnt
=======================

Hallo {{ $absence->user->first_name }},

Der folgende Urlaubsantrag wurde am {{ \Carbon\Carbon::now()->formatLocalized('%A, %d.%m.%Y, um %H:%M Uhr') }} von
{{ $author->name }} abgelehnt.

@component('mail::panel')
**{{ $absence->reason }}**

{{ $absence->from->formatLocalized('%A, %d.%m.%Y') }} bis {{ $absence->from->formatLocalized('%A, %d.%m.%Y') }}
@endcomponent

@if($absence->admin_notes)
Der Überprüfung wurde folgende Notiz beigefügt:

@component('mail::panel')
{!! $absence->admin_notes !!}
@endcomponent

@endif
@if($absence->approver_notes)
Der Bitte um Genehmigung wurde folgende Notiz hinzugefügt:

@component('mail::panel')
{!! $absence->approver_notes !!}
@endcomponent

@endif

@endcomponent
