@extends('layouts.app')

@section('title', 'Herzlich willkommen')

@section('content')
    @component('components.ui.card')
        <h1>Willkommen, {{ $user->name }}!</h1>
        <a class="btn btn-primary btn-lg" href="{{ route('calendar') }}"><span class="fa fa-calendar"></span> Zum Kalender</a>
        <hr />
        @tabheaders
            @foreach ($tabs as $tab){!! $tab->getHeader(['index' => $loop->index]) !!}@endforeach
        @endtabheaders
        @tabs
            @foreach ($tabs as $tab){!! $tab->getContent(['index' => $loop->index]) !!}@endforeach
        @endtabs
    @endcomponent
@endsection
