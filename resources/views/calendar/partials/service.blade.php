<div
        class="service-entry @can('update', $service) editable @endcan
        @if ((!$slave) && $service->participants->contains(Auth::user())) mine @endif
        @if (($slave) && ($highlight == $service->id)) highlighted @endif
        @canany(['gd-kasualien-nur-statistik', 'gd-kasualien-lesen']) @if($service->funerals->count()) funeral @endif @endcanany
                "
        @can('update', $service)
        style="cursor: pointer;"
        title="Klicken, um diesen Eintrag zu bearbeiten"
        onclick="window.location.href='{{ route('services.edit', $service->id) }}';"
        @endcan>
    @if (!is_object($service->location))
        <div class="service-time service-special-time">
            {{ strftime('%H:%M', strtotime($service->time)) }} Uhr
        </div>
        <span class="separator">|</span>
        <div class="service-location service-special-location">{{ $service->special_location }}</div>
    @else
        <div
                class="service-time @if($service->time !== $service->location->default_time) service-special-time @endif">
            {{ strftime('%H:%M', strtotime($service->time)) }} Uhr
        </div>
        <span class="separator">|</span>
        <div class="service-location">{{ $service->location->name }}</div>
    @endif
    @if($service->cc) <img src="{{ env('APP_URL') }}img/cc.png"
                           title="Parallel Kinderkirche ({{ $service->cc_location }}) zum Thema {{ '"'.$service->cc_lesson.'"' }}: {{ $service->cc_staff }}"/> @endif
    @canany(['gd-kasualien-nur-statistik', 'gd-kasualien-lesen'])
        @if($service->weddings->count())
            <div class="service-description">
                <span class="fa fa-ring"></span> @can('gd-kasualien-lesen') {{ $service->weddingsText() }} @endcan
            </div>
        @endif
        @if($service->funerals->count())
            <div class="service-description">
                <span class="fa fa-cross"></span> @can('gd-kasualien-lesen') {{ $service->funeralsText() }} @endcan
            </div>
        @endif
    @endcanany
    <div class="float-right service-calendar-button">
        <a class="btn btn-sm btn-secondary"
           href="{{ route('services.ical', $service) }}"
           title="In Outlook übernehmen"><span
                    class="fa fa-calendar-alt"></span></a>
    </div>
    <div class="service-team service-pastor"><span
                class="designation">P: </span>
        @if ($service->need_predicant)
            <span class="need-predicant">Prädikant benötigt</span>
        @else
            @foreach($service->pastors as $participant)
                <span @can('urlaub-lesen') @if (in_array($participant->lastName(), array_keys($vacations[$day->id]))) class="vacation-conflict"
                      title="Konflikt mit Urlaub!" @endif @endcan>{{ $participant->lastName(true) }}</span>
                @if($loop->last) @else | @endif
            @endforeach
        @endif
    </div>
    <div class="service-team service-organist"><span
                class="designation">O: </span>@foreach($service->organists as $participant){{ $participant->lastName(true) }}@if($loop->last) @else
            | @endif @endforeach</div>
    <div class="service-team service-sacristan"><span
                class="designation">M: </span>@foreach($service->sacristans as $participant){{ $participant->lastName(true) }}@if($loop->last) @else
            | @endif @endforeach</div>
    @foreach($service->ministries() as $ministry => $people)
        <div class="service-team"><span
                    class="designation">{{ $ministry }}: </span>{{ $people->implode('planName', ' | ') }}
        </div>
    @endforeach
    <div class="service-description">{{ $service->descriptionText() }}</div>
    @if($service->internal_remarks)
        <div class="service-description"
             title="{{ $service->internal_remarks }}"><span
                    class="fa fa-eye-slash"
                    title="Anmerkung nur für den internen Gebrauch"></span> {{ \App\Tools\StringTool::trimToLen($service->internal_remarks, 150) }}
        </div>@endif
    @canany(['gd-kasualien-lesen', 'gd-kasualien-nur-statistik'])
        @if($service->baptisms->count())
            <div class="service-description">
                @if($service->baptisms->count()) <span class="fa fa-water"
                                                       @can('gd-kasualien-lesen') @if(Auth::user()->cities->contains($city)) title="{{ $service->baptismsText(true) }}" @endif @endcan ></span> {{ $service->baptisms->count() }} @endif
            </div>
        @endif
    @endcanany
</div>
