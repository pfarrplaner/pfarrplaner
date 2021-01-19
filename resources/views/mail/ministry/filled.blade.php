@component('mail::message')

Zusage
=======


Hallo {{ $sender->name }},

{{ $user->name }} hat zugesagt, bei folgenden Gottesdiensten den Dienst "{{ $ministry }}" zu übernehmen:

@foreach($services as $service)
- {{ $service->day->date->formatLocalized('%A, %d. %B %Y') }}, {{ $service->timeText() }}, {{ $service->locationText() }}
@endforeach


@endcomponent
