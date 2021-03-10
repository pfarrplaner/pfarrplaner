@extends('layouts.app', ['noNavBar' => 1, 'noNav' => 1])

@section('title', 'Dienstanfrage')

@section('content')
    <form method="post" action="{{ route('ministry.request.fill', compact('ministry', 'user', 'sender')) }}">
        @csrf
        @component('components.ui.card')
            @slot('cardHeader')
                Dienstanfrage für "{{ $ministry }}"
            @endslot
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Absenden</button>
            @endslot

            <p>Guten Tag, {{ $user->name }}!</p>
            <p>Bei welchem der folgenden Gottesdienste könnten Sie den Dienst "{{ $ministry }}" übernehmen? Bitte kreuzen Sie einfach an:</p>

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Zusagen</th>
                        <th>Gottesdienst</th>
                        <th>Bereits eingetragen</th>
                    </tr>
                </thead>
                <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td>@checkbox(['name' => 'services['.$service->id.']', 'label' => ''])</td>
                        <td><b>{{$service->day->date->formatLocalized('%A, %d.%m.%Y')}} {{$service->timeText()}}</b><br /> {{$service->locationText()}}</td>
                        <td>{{ $service->participantsText($ministry, true) }}</td>
                    </tr>
                @endforeach

                </tbody>
            </table>

        @endcomponent
    </form>
@endsection
