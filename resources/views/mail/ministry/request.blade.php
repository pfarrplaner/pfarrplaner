@component('mail::message')

Anfrage
=======


Hallo {{ $user->name }},

{{ $sender->name }} fragt an, ob du dir vorstellen könntest, in einem oder mehreren der folgenden Gottesdienste den Dienst
"{{ $ministry }}" zu übernehmen.

@foreach($services as $service)
- {{ $service->day->date->formatLocalized('%A, %d. %B %Y') }}, {{ $service->timeText() }}, {{ $service->locationText() }}
@endforeach

@if($text)

    Dazu hat {{ $sender->name }} noch Folgendes geschrieben:


    {{ $text }}


@endif


Bist du dabei? Dann kannst du hier mit nur ein paar Klicks die entsprechenden Gottesdienste auswählen und direkt zusagen:
@component('mail::button', ['url' => $url])
    Zur Anfrage
@endcomponent



Du hast noch Fragen? Dann wende dich am Besten direkt an {{ $sender->name }} ({{ $sender->email }}).

Herzlichen Dank für deine Bereitschaft zur Mithilfe!
Das Team deiner Kirchengemeinde

@endcomponent
