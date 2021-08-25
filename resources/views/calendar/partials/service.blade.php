<div
        class="service-entry @can('update', $service) editable @endcan
        @if ((!$slave) && $service->participants->contains(Auth::user())) mine @endif
        @if (($slave) && ($highlight == $service->id)) highlighted @endif
        @canany(['gd-kasualien-nur-statistik', 'gd-kasualien-lesen']) @if($service->funerals->count()) funeral @endif @endcanany
        @if($service->hidden) hidden @endif
                "
        @can('update', $service)
        style="cursor: pointer;"
        title="Klicken, um diesen Eintrag zu bearbeiten (#{{ $service->id }})"
        onclick="window.location.href='{{ route('service.edit', $service->id) }}';"
        @endcan
        data-day="{{ $service->day->id }}"
>
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
    @if($service->cc) <img src="{{ asset('img/cc.png') }}"
                           title="Parallel Kinderkirche ({{ $service->cc_location }}) zum Thema {{ '"'.$service->cc_lesson.'"' }}: {{ $service->cc_staff }}"/> @endif
    @if($service->youtube_url)
        <a href="{{ $service->youtube_url }}" target="_blank" style="color: red"; title="Zum Youtube-Video"><span class="fab fa-youtube"></span></a>
        @if ($service->city->youtube_channel_url)
                <a href="{{ \App\Helpers\YoutubeHelper::getLiveDashboardUrl($service->city, $service->youtube_url) }}" target="_blank" style="color: darkgray"; title="Zum LiveDashboard"><span class="fa fa-video"></span></a>
        @endif
    @endif
    @if($service->titleText(true, true) != 'GD')
        <div class="service-description">{{ $service->titleText(true, true) }}</div>
    @endif
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
           href="{{ route('service.ical', $service) }}"
           title="In Outlook übernehmen"><span
                    class="fa fa-calendar-alt"></span></a>
    </div>
    <div class="service-team service-pastor"><span
                class="designation">P: </span>
        @if ($service->need_predicant)
            <span class="need-predicant">Prädikant benötigt</span>
        @else
            @include('calendar.partials.peoplelist', ['participants' => $service->pastors, 'vacation_check' => true])
        @endif
    </div>
    <div class="service-team service-organist"><span
                class="designation">O: </span>
        @include('calendar.partials.peoplelist', ['participants' => $service->organists, 'vacation_check' => false])
    </div>
    <div class="service-team service-sacristan"><span
                class="designation">M: </span>
        @include('calendar.partials.peoplelist', ['participants' => $service->sacristans, 'vacation_check' => false])
    </div>
    @foreach($service->ministries() as $ministry => $people)
        <div class="service-team"><span
                    class="designation">{{ $ministry }}: </span>
            @include('calendar.partials.peoplelist', ['participants' => $people, 'vacation_check' => false])
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
