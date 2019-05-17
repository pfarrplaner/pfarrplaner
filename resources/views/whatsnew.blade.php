@extends('layouts.app')

@section('title', 'Neue Funktionen')

@section('content')
    @component('components.container')
        <h1>Neue Funktionen</h1>
        @foreach($messages as $message)
            <div class="alert alert-info">
                <b>{{ $message['date']->format('d.m.Y') }}</b>: <br />
                {!! $message['text'] !!}
            </div>
        @endforeach
    @endcomponent
@endsection
