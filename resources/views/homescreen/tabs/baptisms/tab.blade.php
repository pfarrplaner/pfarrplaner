@tab(['id' => $tab->getKey(), 'active' => ($index == 0)])
    <h2>{{ $tab->getTitle() }}</h2>
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
@if($config['showRequests'])
    @tab(['id' => $tab->getKey().'Requests'])
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
                        <a class="btn btn-sm btn-primary"
                           href="{{route('baptisms.edit', $baptism->id)}}"
                           title="Bearbeiten"><span class="fa fa-edit"></span> <span
                                class="fa fa-water"></span></a>
                        <form action="{{ route('baptisms.destroy', $baptism->id) }}" method="post">
                            @method('DELETE')
                            @csrf
                            <button class="btn btn-sm btn-danger"><span class="fa fa-trash"></span>
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    @endtab
@endif
