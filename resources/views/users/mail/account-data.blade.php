@component('mail::message')
Deine Zugangsdaten für den Pfarrplaner
======================================

Hallo {{ $user->first_name }},

Mit den folgenden Anmeldedaten kannst du dich am **Pfarrplaner** anmelden:

@component('mail::panel')
**Anmeldeseite**: {{ config('app.url') }}

**Benutzername**:  ````{{ $user->email }}````

**Passwort**: ````{{ $password }}````

@endcomponent

@component('mail::button', ['url' => config('app.url')])
    Jetzt anmelden
@endcomponent

*Bitte beachte*: Das Passwort muss bei der nächsten Anmeldung geändert werden.

@endcomponent
