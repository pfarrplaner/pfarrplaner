@extends('layouts.app')

@section('title', 'Herzlich willkommen')

@section('content')
    @component('components.ui.card')
        <h1>Willkommen, {{ $user->first_name ?: $user->name }}!</h1>
        <a class="btn btn-primary btn-lg" href="{{ route('calendar') }}"><span class="fa fa-calendar"></span> Zum Kalender</a>
        @if(\Illuminate\Support\Facades\Auth::user()->getSetting('homeScreenConfig', ['wizardButtons' => 0])['wizardButtons'])
            <a class="btn btn-secondary btn-lg" href="{{ route('baptisms.create') }}"><span class="fa fa-water"></span>
                Taufe
                anlegen...</a>
            <a class="btn btn-secondary btn-lg" href="{{ route('funerals.wizard') }}"><span class="fa fa-cross"></span>
                Beerdigung anlegen...</a>
            <a class="btn btn-secondary btn-lg" href="{{ route('weddings.wizard') }}"><span class="fa fa-ring"></span>
                Trauung
                anlegen...</a>
        @endif
        <hr />
        @if(count($tabs) >0 )
            @tabheaders
                @foreach ($tabs as $tab){!! $tab->getHeader(['index' => $loop->index]) !!}@endforeach
            @endtabheaders
            @tabs
                @foreach ($tabs as $tab){!! $tab->getContent(['index' => $loop->index]) !!}@endforeach
            @endtabs
        @else
            <p>Hier gibt es noch nicht viel zu sehen. In deinem <a href="{{ route('user.profile') }}">Profil</a> kannst du die Inhalte deines Startbildschirms selbst einstellen.</p>
        @endif
    @endcomponent
@endsection
