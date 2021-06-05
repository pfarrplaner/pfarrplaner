@extends('layouts.app')

@section('title', 'Über Pfarrplaner')

@section('content')
    @component('components.ui.card')
        @slot('cardHeader')
            Informationen zur App
        @endslot
        <a href="{{ config('app.build_repository') }}" target="_blank">Pfarrplaner</a><br />
        &copy; 2018-{{ \Carbon\Carbon::now()->format('Y') }} <a href="https://christoph-fischer.de/" target="_blank">Pfarrer Christoph Fischer</a><br /><br />
        Build <a href="{{ config('app.build_url') }}" target="_blank">#{{ config('app.build_number') }}</a>
        ({{ config('app.build') }})<br />
        vom {{ Carbon\Carbon::createFromTimeString(config('app.build_date'))->setTimezone('Europe/Berlin')->formatLocalized('%A, %d. %B %Y, %H:%M:%S Uhr') }}
        <br /><br />
        Gehostet auf einem Server des <a href="https://www.kirchenbezirk-balingen.de/" target="_blank">Evangelischen Kirchenbezirks Balingen</a>.<br /><br />
        Der Quellcode von Pfarrplaner ist als Open Source auf <a href="https://github.com/pfarrplaner/pfarrplaner" target="_blank">GitHub</a> verfügbar und
        steht unter der <a href="https://github.com/pfarrplaner/pfarrplaner/blob/master/LICENSE" target="_blank">GNU General Public License (GPL) 3.0 oder höher</a>.

    @endcomponent

    @if(count($history))
    @component('components.ui.card')
        @slot('cardHeader')
            Letzte Änderungen (Git changelog)
        @endslot
        <ul>
        @foreach ($history as $commit)
            <li><b>{{ \Carbon\Carbon::createFromTimeString($commit['date'])->formatLocalized('%A, %d. %B %Y, %H:%M:%S Uhr') }}</b>
                &middot; {{ $commit['author'] }}
                &middot; <a href="https://github.com/pfarrplaner/pfarrplaner/commit/{{ $commit['hash'] }}" target="_blank">{{ $commit['hash'] }}</a><br />
                {!! nl2br($commit['message']) !!}
                <br />
            </li>
        @endforeach
        </ul>
    @endcomponent
    @endif


@endsection
