@extends('layouts.app')

@section('title', 'Passwort vergessen')


@section('content')
    @component('components.container')
        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            @component('components.ui.card')
                @slot('cardHeader')
                    Passwort vergessen
                @endslot
                @slot('cardFooter')
                    <button type="submit" class="btn btn-primary">
                        Link zum Zurücksetzen des Passworts senden
                    </button>
                @endslot
                <p>Du hast dein Passwort vergessen? Keine Sorge, wir senden dir eine E-Mail mit einem Link, über den du ein neues Passwort setzen kannst.</p>
                @input(['name' => 'email', 'label' => 'E-Mailadresse', 'value' => old('email')])
            @endcomponent
        </form>
    @endcomponent
@endsection
