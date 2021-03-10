@canany(['gd-kasualien-nur-statistik', 'gd-kasualien-lesen', 'gd-kasualien-barbeiten'])
@if($service->titleText(true, true) != 'GD')
    <div class="service-description"><b>{{ $service->titleText(true, true) }}</b></div>
@endif
@if($service->weddings->count())
    <div class="service-description">
        <span class="fa fa-ring"></span> @canany(['gd-kasualien-lesen', 'gd-kasualien-barbeiten']) {{ $service->weddingsText() }} @endcanany
    </div>
@endif
@if($service->funerals->count())
    <div class="service-description">
        <span class="fa fa-cross"></span> @canany(['gd-kasualien-lesen', 'gd-kasualien-barbeiten']) {{ $service->funeralsText() }} @endcanany
    </div>
@endif
@endcanany
<div class="service-team service-pastor"><span
            class="designation">P: </span>
    @if ($service->need_predicant)
        <span class="need-predicant">Prädikant benötigt</span>
    @else
        {{ $service->participantsText('P') }}
    @endif
</div>
<div class="service-team service-organist"><span
            class="designation">O: </span>{{ $service->participantsText('O') }}</div>
<div class="service-team service-sacristan"><span
            class="designation">M: </span>{{ $service->participantsText('M') }}</div>
@foreach($service->ministries() as $ministry => $people)
    <div class="service-team"><span class="designation">{{ $ministry }}: </span>{{ $people->implode('planName', ' | ') }}</div>
@endforeach
<div class="service-description">{{ $service->descriptionText() }}</div>
@if($service->internal_remarks)<div class="service-description" title="{{ $service->internal_remarks }}"><span class="fa fa-eye-slash" title="Anmerkung nur für den internen Gebrauch"></span> {{ \App\Tools\StringTool::trimToLen($service->internal_remarks, 150) }}</div>@endif
<div class="service-description">
@canany(['gd-kasualien-nur-statistik', 'gd-kasualien-lesen', 'gd-kasualien-barbeiten'])
@if($service->baptisms->count())
        @if($service->baptisms->count()) <span class="fa fa-water" title="@canany(['gd-kasualien-lesen', 'gd-kasualien-barbeiten']){{ $service->baptismsText(true) }}" @endcanany></span> {{ $service->baptisms->count() }} @endif
@endif
@endcanany
@can('edit', $service)
    @if ($service->commentsForCurrentUser->count() > 0)
        <span class="fa fa-comments"></span>&nbsp;{{ $service->commentsForCurrentUser->count() }}
    @endif
@endcan
</div>

@if($service->cc) <img src="{{ env('APP_URL') }}img/cc.png" title="Parallel Kinderkirche ({{ $service->cc_location }}) zum Thema {{ '"'.$service->cc_lesson.'"' }}: {{ $service->cc_staff }}"/> @endif
<textarea id="service-credits-{{ $service->id }}" style="position: absolute;   opacity: .01;   height: 0;   overflow: hidden;">{{ $service->credits }}</textarea>
<button class="btn btn-sm btn-secondary" onclick="var el= document.getElementById('service-credits-{{ $service->id }}'); el.focus(); el.select(); document.execCommand('copy'); "><span class="fa fa-clipboard"></span> Credits kopieren</button>
