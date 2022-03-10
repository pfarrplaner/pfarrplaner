@extends('layouts.public')

@section('title')Dienstplan {{ $ministries->implode(', ') }} für {{ $city->name }} @endsection

@section('contents')
    <main class="py-1 container">
        @component('components.container')
            <h1 class="mt-2 mb-4">Dienstplan {{ $ministries->implode(', ') }} für {{ $city->name }}</h1>

            <table class="table table-fluid table-striped">
                <thead>
                <tr>
                    <th>Gottesdienst</th>
                    <th>Verantwortlich</th>
                    @foreach($ministries as $ministry)
                    <th>{{ $ministry }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($services as $service)
                    <tr>
                        <td>
                            @if (($service->titleText() != 'GD') && ($service->titleText()!='Gottesdienst'))
                                <i>{{ $service->titleText() }}</i><br />
                            @endif
                            {{ $service->date->format('d.m.Y') }}, {{ $service->timeText() }}<br />
                            {{ $service->locationText() }}
                        </td>
                        <td>
                            P: {{ $service->participantsText('P', true) }}<br />
                            O: {{ $service->participantsText('O', true) }}<br />
                            M: {{ $service->participantsText('M', true) }}<br />
                        </td>
                            @foreach ($ministries as $ministry)
                            <td>
                                {{ $service->participantsText($ministry, true) }}<br />
                            </td>
                            @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endcomponent
    </main>
@endsection
