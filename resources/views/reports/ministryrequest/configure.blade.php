@extends('layouts.app')

@section('title', 'Dienstanfrage senden')

@section('content')
    <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'addresses']) }}">
        @csrf
        @component('components.ui.card')
            @slot('cardHeader')
                Dienstanfrage erstellen (Schritt 2)
            @endslot
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Weiter &gt;</button>
            @endslot

            @peopleselect(['name' => 'recipients[]', 'label' => 'Anfrage senden an', 'people' => \App\User::all(), 'value' => $users ])

            <label>Folgende Gottesdienste anfragen</label>
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
                            <td>@checkbox(['name' => 'services['.$service->id.']', 'label' => '', 'value' => ($service->participantsText($ministry) == '')])</td>
                            <td><b>{{$service->day->date->formatLocalized('%A, %d.%m.%Y')}} {{$service->timeText()}}</b><br /> {{$service->locationText()}}</td>
                            <td>{{ $service->participantsText($ministry) }}</td>
                        </tr>
                    @endforeach

                    </tbody>
                </table>

            @hidden(['name' => 'locations', 'value' => join(',', $locations)])
            @hidden(['name' => 'start', 'value' => $start->format('d.m.Y')])
            @hidden(['name' => 'end', 'value' => $end->format('d.m.Y')])
            @hidden(['name' => 'ministry', 'value' => $ministry])


        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        $('.peopleSelect').selectize({
            create: true,
            render: {
                option_create: function (data, escape) {
                    return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                }
            },
        });
    </script>
@endsection
