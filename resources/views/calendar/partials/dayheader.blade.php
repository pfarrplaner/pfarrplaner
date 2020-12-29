<th class="hide-buttons @if(isset($nextDay) && ($day->date->format('Ymd')==$nextDay->date->format('Ymd'))) now @endif
@if($day->day_type == \App\Day::DAY_TYPE_LIMITED) limited collapsed @endif
@if($day->day_type == \App\Day::DAY_TYPE_LIMITED && (count($day->cities->intersect(Auth::user()->cities))==0)) not-for-city @endif
        "
    @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) title="{{ $day->date->format('d.m.Y') }} (Klicken, um Ansicht umzuschalten)"
    @endif
    data-day="{{ $day->id }}"
>
    <div class="day-header-collapse-hover">{{ $day->date->formatLocalized('%a, %d.') }}</div>
    <div class="card card-effect">
        <div class="card-header day-header-{{ $day->date->formatLocalized('%a') }}">
            {{ $day->date->formatLocalized('%A') }}
        </div>
        <div class="card-body">
            {{ $day->date->format('d') }}
            @can('tag-loeschen')
                <form action="{{ route('days.destroy', $day->id)}}" method="post"
                      style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm" type="submit"><span
                                class="fa fa-trash"></span>
                    </button>
                </form>
            @endcan
            @if (isset($day->liturgy['perikope']))
                <div class="liturgy">
                    <div class="liturgy-sermon">
                        <div class="liturgy-color"
                             style="background-color: {{ $day->liturgy['litColor'].';'.($day->liturgy['litColor'] == 'white' ? 'border-color: darkgray;' : '') }}"
                             title="{{ $day->liturgy['feastCircleName'] }}"></div>
                        <a href="{{ $day->liturgy['litTextsPerikope'.$day->liturgy['perikope'].'Link'] }}"
                           target="_blank" title="Klicken, um den Text in einem neuen Tab zu lesen">
                            {{ $day->liturgy['litTextsPerikope'.$day->liturgy['perikope']] }}</a>
                    </div>
                </div>
                @else
                <div class="liturgy"></div>
            @endif
        </div>
        @if ($day->name)
            <div class="card-footer day-name"
                 title="@if(isset($day->liturgy['litProfileGist'])){{ $day->liturgy['litProfileGist'] }}@endif">{{ $day->name }}</div>
        @else
            <div class="card-footer day-name"
                 title="@if(isset($day->liturgy['litProfileGist'])){{ $day->liturgy['litProfileGist'] }}@endif">@if(isset($day->liturgy['title'])){{ $day->liturgy['title'] }}@endif</div>
        @endif
    </div>

    @if ($day->description)
        <div class="day-description">{{ $day->description }}</div> @endif
    @can('urlaub-lesen')
        @foreach ($absences as $absence)
            @if($absence->containsDate($day->date))
                <div class="vacation"
                     title="{{ $absence->user->fullName(true) }}: {{ $absence->reason }} ({{ $absence->durationText() }}) [{{ $absence->replacementText('V:') }}]">
                    <span class="fa fa-globe-europe"></span> {{ $absence->user->lastName() }}</div>
            @endif
        @endforeach
    @endcan
</th>
