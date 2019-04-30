@extends('layouts.app')

@section('title', 'Herzlich willkommen')

@section('content')
    @component('components.container')
        <h1>Willkommen, {{ $user->name }}!</h1>
        <a class="btn btn-primary btn-lg" href="{{ route('calendar') }}"><span class="fa fa-calendar"></span> Zum Kalender</a>
        <hr />

        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item active">
                <a class="nav-link active" href="#services" role="tab" data-toggle="tab">Meine Gottesdienste @if($services->count())<span class="badge badge-primary">{{ $services->count() }}</span> @endif </a>
            </li>
        </ul>

        <div class="tab-content">
            <br />
            <div role="tabpanel" class="tab-pane fade in active show" id="services">
                <h2>Meine nächsten Gottesdienste</h2>
                <p>Angezeigt werden die Gottesdienste der nächsten 2 Monate</p>
                <div class="table-responsive">
                    <table class="table table-striped" width="100%">
                        <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Zeit</th>
                            <th>Ort</th>
                            <th>Details</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($services as $service)
                            <tr>
                                <td>{{ $service->day->date->format('d.m.Y') }}</td>
                                <td>{{ $service->timeText() }}</td>
                                <td>{{ $service->locationText() }}</td>
                                <td>
                                    @include('partials.service.details', ['service' => $service])
                                </td>
                                <td>
                                    @include('partials.service.edit-block', ['service' => $service])
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endcomponent
@endsection