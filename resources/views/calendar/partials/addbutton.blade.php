@if ((!$slave) && Auth::user()->can('create', \App\Service::class) && Auth::user()->writableCities->contains($city))
    <a class="btn btn-success btn-sm btn-add-day"
       title="Neuen Gottesdiensteintrag hinzufügen"
       href="{{ route('services.add', ['date' => $day->id, 'city' => $city->id]) }}"><span
                class="fa fa-plus" title="Neuen Gottesdienst hinzufügen"></span><span class="d-none d-md-inline"> Neuer GD</span></a>
@endif
