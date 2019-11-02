@extends('layouts.app')

@section('title', 'Herzlich willkommen')

@section('content')
    @component('components.container')
        <h1>Willkommen, @if($user->first_name != ''){{$user->first_name}}! @else {{ $user->name }}! @endif</h1>
        <a class="btn btn-primary btn-lg" href="{{ route('calendar') }}"><span class="fa fa-calendar"></span> Zum Kalender</a>
        <a class="btn btn-secondary btn-lg" href="{{ route('baptisms.create') }}"><span class="fa fa-water"></span> Taufe anlegen...</a>
        <a class="btn btn-secondary btn-lg" href="{{ route('funerals.wizard') }}"><span class="fa fa-cross"></span> Beerdigung anlegen...</a>
        <a class="btn btn-secondary btn-lg" href="{{ route('weddings.wizard') }}"><span class="fa fa-ring"></span> Trauung anlegen...</a>
        <hr />

        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item active">
                <a class="nav-link active" href="#services" role="tab" data-toggle="tab">Meine Gottesdienste @if($services->count())<span class="badge badge-primary">{{ $services->count() }}</span> @endif </a>
            </li>
            @canany(['gd-kasualien-lesen', 'gd-kasualien-bearbeiten'])
            <li class="nav-item">
                <a class="nav-link" href="#baptisms" role="tab" data-toggle="tab">Meine Taufen @if($baptisms->count())<span class="badge badge-primary">{{ $baptisms->count() }}</span> @endif </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#baptismRequests" role="tab" data-toggle="tab">Taufanfragen @if($baptismRequests->count())<span class="badge badge-primary">{{ $baptismRequests->count() }}</span> @endif </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#funerals" role="tab" data-toggle="tab">Meine Beerdigungen @if($funerals->count())<span class="badge badge-primary">{{ $funerals->count() }}</span> @endif </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="#weddings" role="tab" data-toggle="tab">Meine Trauungen @if($weddings->count())<span class="badge badge-primary">{{ $weddings->count() }}</span> @endif </a>
            </li>
            @endcanany
            @if(Auth::user()->manage_absences)
                <li class="nav-item" id="absenceTab">
                    <a class="nav-link" href="#absences" role="tab" data-toggle="tab">Mein Urlaub</a>
                </li>
            @endif
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
                                    @include('partials.service.details')
                                </td>
                                <td>
                                    @include('partials.service.edit-rites-block', ['service', $service])
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @canany(['gd-kasualien-lesen', 'gd-kasualien-bearbeiten'])
            <div role="tabpanel" class="tab-pane fade" id="baptisms">
                <h2>Meine Taufen</h2>
                <p>Angezeigt werden alle bereits bekannten Taufen in den nächsten 365 Tagen.</p>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Zeit</th>
                            <th>Ort</th>
                            <th>Details</th>
                            <th>Täufling</th>
                            <th>Erstkontakt</th>
                            <th>Taufgespräch</th>
                            <th>Anmeldung</th>
                            <th>Urkunden</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($baptisms as $service)
                            @foreach($service->baptisms as $baptism)
                                <tr>
                                    @if ($loop->first)
                                        <td rowspan="{{ $service->baptisms->count() }}">{{ $service->day->date->format('d.m.Y') }}</td>
                                        <td rowspan="{{ $service->baptisms->count() }}">{{ $service->timeText() }}</td>
                                        <td rowspan="{{ $service->baptisms->count() }}">{{ $service->locationText() }}</td>
                                        <td rowspan="{{ $service->baptisms->count() }}">
                                            @include('partials.service.details', ['service' => $service])
                                        </td>
                                    @endif
                                    @include('partials.baptism.details', ['baptism' => $baptism])
                                    @if ($loop->first)
                                        <td rowspan="{{ $service->baptisms->count() }}">
                                            @include('partials.service.edit-rites-block', ['service', $service])
                                            @can('gd-kasualien-bearbeiten')
                                                <a class="btn btn-sm btn-primary" href="{{route('baptisms.edit', $baptism)}}?back=/home" title="Taufe bearbeiten"><span class="fa fa-edit"></span> <span class="fa fa-water"></span></a>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="baptismRequests">
                <h2>Taufanfragen</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Täufling</th>
                            <th>Erstkontakt</th>
                            <th>Taufgespräch</th>
                            <th>Anmeldung</th>
                            <th>Urkunden</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($baptismRequests as $baptism)
                            <tr class="">
                                @include('partials.baptism.details', ['baptism' => $baptism])
                                <td>
                                    <a class="btn btn-sm btn-primary" href="{{route('baptisms.edit', $baptism->id)}}" title="Bearbeiten"><span class="fa fa-edit"></span> <span class="fa fa-water"></span></a>
                                    <form action="{{ route('baptisms.destroy', $baptism->id) }}" method="post">
                                        @method('DELETE')
                                        @csrf
                                        <button class="btn btn-sm btn-danger"><span class="fa fa-trash"></span></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="funerals">
                <h2>Meine Beerdigungen</h2>
                <p>Angezeigt werden alle bereits bekannten Beerdigungen.</p>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Zeit</th>
                            <th>Ort</th>
                            <th>Details</th>
                            <th>Person</th>
                            <th>Bestattungsart</th>
                            <th>Trauergespräch</th>
                            <th>Abkündigung</th>
                            <th></th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($funerals as $service)
                            @foreach($service->funerals as $funeral)
                                <tr>
                                    @if ($loop->first)
                                        <td rowspan="{{ $service->funerals->count() }}">{{ $service->day->date->format('d.m.Y') }}</td>
                                        <td rowspan="{{ $service->funerals->count() }}">{{ $service->timeText() }}</td>
                                        <td rowspan="{{ $service->funerals->count() }}">{{ $service->locationText() }}</td>
                                        <td rowspan="{{ $service->funerals->count() }}">
                                            @include('partials.service.details', ['service' => $service])
                                        </td>
                                    @endif
                                    @include('partials.funeral.details', ['funeral' => $funeral])
                                        <td>
                                            <a class="btn btn-sm btn-secondary" href="{{ route('funeral.form', $funeral->id) }}" title="Formular für Kirchenregisteramt"><span class="fa fa-file-pdf"></span></a>
                                        </td>
                                    @if ($loop->first)
                                        <td rowspan="{{ $service->funerals->count() }}">
                                            <a class="btn btn-sm btn-secondary" href="{{ route('calendar', ['month' => $service->day->date->format('m'), 'year' => $service->day->date->format('Y')]) }}" title="Im Kalender ansehen"><span class="fa fa-calendar"></span></a>
                                            <a class="btn btn-sm btn-primary" href="{{route('services.edit', $service->id).'#rites'}}" title="Bearbeiten"><span class="fa fa-edit"></span></a>
                                            @can('gd-kasualien-bearbeiten')
                                                <a class="btn btn-sm btn-primary" href="{{route('funerals.edit', $funeral)}}?back=/home" title="Beerdigung bearbeiten"><span class="fa fa-edit"></span> <span class="fa fa-cross"></span></a>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div role="tabpanel" class="tab-pane fade" id="weddings">
                <h2>Meine Trauungen</h2>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Datum</th>
                            <th>Zeit</th>
                            <th>Ort</th>
                            <th>Details</th>
                            <th>Ehepartner 1</th>
                            <th>Ehepartner 2</th>
                            <th>Traugespräch</th>
                            <th>Anmeldung</th>
                            <th>Urkunden</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($weddings as $service)
                            @foreach($service->weddings as $wedding)
                                <tr>
                                    @if ($loop->first)
                                        <td rowspan="{{ $service->weddings->count() }}">{{ $service->day->date->format('d.m.Y') }}</td>
                                        <td rowspan="{{ $service->weddings->count() }}">{{ $service->timeText() }}</td>
                                        <td rowspan="{{ $service->weddings->count() }}">{{ $service->locationText() }}</td>
                                        <td rowspan="{{ $service->weddings->count() }}">
                                            @include('partials.service.details', ['service' => $service])
                                        </td>
                                    @endif
                                    @include('partials.wedding.details', ['wedding' => $wedding])
                                    @if ($loop->first)
                                        <td rowspan="{{ $service->weddings->count() }}">
                                            @include('partials.service.edit-rites-block', ['service', $service])
                                            @can('gd-kasualien-bearbeiten')
                                                <a class="btn btn-sm btn-primary" href="{{route('weddings.edit', $wedding)}}?back=/home" title="Trauung bearbeiten"><span class="fa fa-edit"></span> <span class="fa fa-ring"></span></a>
                                            @endcan
                                        </td>
                                    @endif
                                </tr>
                            @endforeach
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            @endcanany
            @if(Auth::user()->manage_absences)
                @include('homescreen.tabs.absences')
            @endif
        </div>
    @endcomponent
@endsection