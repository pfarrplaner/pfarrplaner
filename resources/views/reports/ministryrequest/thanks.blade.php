@extends('layouts.app', ['noNavBar' => 1, 'noNav' => 1])

@section('title', 'Dienstanfrage')

@section('content')
    <form method="post" action="{{ route('ministry.request.fill', compact('ministry', 'user')) }}">
        @csrf
        @component('components.ui.card')
            @slot('cardHeader')
                Dienstanfrage für "{{ $ministry }}"
            @endslot

            <p>Herzlichen Dank, {{ $user->name }}!</p>
            <p>Deine Zusagen wurden eingetragen. Danke für deine Bereitschaft zur Mitarbeit.</p>


        @endcomponent
    </form>
@endsection
