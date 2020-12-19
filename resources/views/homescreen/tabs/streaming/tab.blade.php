@tab(['id' => $tab->getKey(), 'active' => ($index == 0)])
    <h2>{{ $tab->getTitle() }}</h2>
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
                        <a class="btn btn-primary" href="{{ \App\Helpers\YoutubeHelper::getLiveDashboardUrl($service->city, $service->youtube_url) }}" target="_blank">
                            <span class="fa fa-video"></span> Live-Dashboard
                        </a>
                        <a class="btn btn-secondary" href="{{ $service->youtube_url }}" target="_blank"><span class="fab fa-youtube"></span> Video</a>
                        <a class="btn btn-secondary" href="{{ route('broadcast.refresh', $service) }}"><span class="fa fa-sync" title="Datenabgleich mit YouTube"></span></a>
                        <form class="form-inline" method="post" action="{{ route('broadcast.delete', $service) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" title=""><span class="fa fa-trash"></span></button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endtab
