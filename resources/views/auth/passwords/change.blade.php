@extends('layouts.app')

@section('content')
    <form method="post" action="{{ route('password.change') }}">
    @csrf
    @component('components.ui.card')
        @slot('cardHeader')
            Wähle ein Passwort
        @endslot
        @slot('cardFooter')
            <input class="btn btn-primary" type="submit" value="Passwort speichern" />
        @endslot
            @if (\Illuminate\Support\Facades\Hash::check('testtest', Auth::user()->password))
                <p>Du verwendest noch das Originalpasswort, das du von deinem Administrator bekommen hast. Bitte wähle ein neues, eigenes Passwort, das nur du selbst kennst.
                </p>
            @else
                @input(['name' => 'current_password', 'label' => 'Aktuelles Passwort', 'type' => 'password'])
            @endif
            @input(['name' => 'new_password', 'label' => 'Neues Passwort', 'type' => 'password'])
            @input(['name' => 'new_password_confirmation', 'label' => 'Neues Passwort wiederholen', 'type' => 'password'])
    @endcomponent
    </form>
@endsection
