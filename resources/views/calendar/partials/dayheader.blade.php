<th class="hide-buttons @if($day->date->format('Ymd')==$nextDay->date->format('Ymd')) now @endif
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
            @if (isset($liturgy[$day->id]['perikope']))
                <div class="liturgy">
                    <div class="liturgy-sermon">
                        <div class="liturgy-color"
                             style="background-color: {{ $liturgy[$day->id]['litColor'].';'.($liturgy[$day->id]['litColor'] == 'white' ? 'border-color: darkgray;' : '') }}"
                             title="{{ $liturgy[$day->id]['feastCircleName'] }}"></div>
                        <a href="{{ $liturgy[$day->id]['litTextsPerikope'.$liturgy[$day->id]['perikope'].'Link'] }}"
                           target="_blank" title="Klicken, um den Text in einem neuen Tab zu lesen">
                            {{ $liturgy[$day->id]['litTextsPerikope'.$liturgy[$day->id]['perikope']] }}</a>
                    </div>
                </div>
                @else
                <div class="liturgy"></div>
            @endif
        </div>
        @if ($day->name)
            <div class="card-footer day-name"
                 title="@if(isset($liturgy[$day->id]['litProfileGist'])){{ $liturgy[$day->id]['litProfileGist'] }}@endif">{{ $day->name }}</div>
        @else
            <div class="card-footer day-name"
                 title="@if(isset($liturgy[$day->id]['litProfileGist'])){{ $liturgy[$day->id]['litProfileGist'] }}@endif">@if(isset($liturgy[$day->id]['title'])){{ $liturgy[$day->id]['title'] }}@endif</div>
        @endif
    </div>

    @if ($day->description)
        <div class="day-description">{{ $day->description }}</div> @endif
    @can('urlaub-lesen')
        @if (isset($vacations[$day->id]) && count($vacations[$day->id]))
            @foreach ($vacations[$day->id] as $vacation)
                <div class="vacation"
                     title="{{ $vacation->user->fullName(true) }}: {{ $vacation->reason }} ({{ $vacation->durationText() }}) [{{ $vacation->replacementText('V:') }}]">
                    <span class="fa fa-globe-europe"></span> {{ $vacation->user->lastName() }}
                </div>
            @endforeach
        @endif
    @endcan
</th>
