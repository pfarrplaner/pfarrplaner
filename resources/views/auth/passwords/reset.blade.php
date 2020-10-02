@extends('layouts.app')
@section('title', 'Passwort zurücksetzen')

@section('content')
    @component('components.container')
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @component('components.ui.card')
                @slot('cardHeader')
                    Passwort zurücksetzen
                @endslot
                @slot('cardFooter')
                        <button type="submit" class="btn btn-primary">
                            Neues Passwort speichern
                        </button>
                @endslot

                @hidden(['name' => 'token', 'value' => $token])
                @input(['name' => 'email', 'type' => 'email', 'label' => 'E-Mailadresse', 'value' => $email ?? old('email'), 'id' => 'email'])
                @input(['name' => 'password', 'type' => 'password', 'label' => 'Neues Passwort'])
                @input(['name' => 'password_confirmation', 'type' => 'password', 'label' => 'Neues Passwort wiederholen'])
            @endcomponent
        </form>
    @endcomponent
@endsection
