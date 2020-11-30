@extends('layouts.app')

@section('title', 'Kalender verbinden')

@section('content')
    <form method="post" action="{{ route('calendarConnection.configure') }}">
        @csrf
        @component('components.ui.card')
            @slot('cardHeader')
                Mit @elkw.de-Konto verbinden
            @endslot
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Weiter &gt;</button>
            @endslot
            @input(['label' => 'Benutzername', 'name' => 'user', 'value' => Auth::user()->email, 'placeholder' => 'Deine @elkw.de-Adresse'])
            @input(['label' => 'Passwort', 'name' => 'password', 'type' => 'password', 'placeholder' => 'Dein ELKW-Passwort'])
        @endcomponent
    </form>
@endsection
