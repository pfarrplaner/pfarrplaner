@tab(['id' => $tab->getKey(), 'active' => ($index == 0)])
    <h2>{{ $tab->getTitle() }}</h2>
    <h3>Beerdigungen</h3>

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
                <tr class="@if($service->day->date < \Carbon\Carbon::now())past @else future @endif">
                    @if ($loop->first)
                        <td rowspan="{{ $service->funerals->count() }}">{{ $service->day->date->format('d.m.Y') }}</td>
                        <td rowspan="{{ $service->funerals->count() }}">{{ $service->timeText() }}</td>
                        <td rowspan="{{ $service->funerals->count() }}">{{ $service->locationText() }}</td>
                        <td rowspan="{{ $service->funerals->count() }}">
                            {{ $service->participantsText('P') }}
                        </td>
                    @endif
                    @include('partials.funeral.details', ['funeral' => $funeral])
                    <td>
                        <a class="btn btn-sm btn-secondary"
                           href="{{ route('funeral.form', $funeral->id) }}"
                           title="Formular für Kirchenregisteramt"><span
                                class="fa fa-file-pdf"></span></a>
                    </td>
                    @if ($loop->first)
                        <td rowspan="{{ $service->funerals->count() }}">
                            <a class="btn btn-sm btn-secondary"
                               href="{{ route('calendar', ['month' => $service->day->date->format('m'), 'year' => $service->day->date->format('Y')]) }}"
                               title="Im Kalender ansehen"><span class="fa fa-calendar"></span></a>
                            <a class="btn btn-sm btn-primary"
                               href="{{route('services.edit', $service->id).'#rites'}}"
                               title="Bearbeiten"><span class="fa fa-edit"></span></a>
                            @can('gd-kasualien-bearbeiten')
                                <a class="btn btn-sm btn-primary"
                                   href="{{route('funerals.edit', $funeral)}}?back=/home"
                                   title="Beerdigung bearbeiten"><span class="fa fa-edit"></span>
                                    <span
                                        class="fa fa-cross"></span></a>
                            @endcan
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>

<hr />
<h3>Trauungen</h3>
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
                            {{ $service->participantsText('P') }}
                        </td>
                    @endif
                    @include('partials.wedding.details', ['wedding' => $wedding])
                    @if ($loop->first)
                        <td rowspan="{{ $service->weddings->count() }}">
                            @include('partials.service.edit-rites-block', ['service', $service])
                            @can('gd-kasualien-bearbeiten')
                                <a class="btn btn-sm btn-primary"
                                   href="{{route('weddings.edit', $wedding)}}?back=/home"
                                   title="Trauung bearbeiten"><span class="fa fa-edit"></span> <span
                                        class="fa fa-ring"></span></a>
                            @endcan
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>

<hr />
<h3>Taufen</h3>
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
                            {{ $service->participantsText('P') }}
                        </td>
                    @endif
                    @include('partials.baptism.details', ['baptism' => $baptism])
                    @if ($loop->first)
                        <td rowspan="{{ $service->baptisms->count() }}">
                            @include('partials.service.edit-rites-block', ['service', $service])
                            @can('gd-kasualien-bearbeiten')
                                <a class="btn btn-sm btn-primary"
                                   href="{{route('baptisms.edit', $baptism)}}?back=/home"
                                   title="Taufe bearbeiten"><span class="fa fa-edit"></span> <span
                                        class="fa fa-water"></span></a>
                            @endcan
                        </td>
                    @endif
                </tr>
            @endforeach
        @endforeach
        </tbody>
    </table>
</div>


@endtab
