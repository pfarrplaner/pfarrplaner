@extends('layouts.app')

@section('title', 'Neue Features')

@section('content')
    @component('components.ui.card')
        @foreach($messages as $message)
            <div class="callout callout-info">
                <b>{{ $message['date']->format('d.m.Y') }}</b> <small>{{ $message['date']->diffForHumans() }}</small> <br />
                {!! $message['text'] !!}
            </div>
        @endforeach
    @endcomponent
@endsection
