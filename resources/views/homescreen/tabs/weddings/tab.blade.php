@tab(['id' => $tab->getKey(), 'active' => ($index == 0)])
    <h2>{{ $tab->getTitle() }}</h2>
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
@endtab
