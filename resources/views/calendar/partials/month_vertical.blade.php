@component('components.ui.loader')
    <div class="calendar-month">
        @if(session()->get('success'))
            <div class="alert alert-success">
                {{ session()->get('success') }}
            </div><br/>
        @endif

        <h1 class="print-only">{{ strftime('%B %Y', $days->first()->date->getTimestamp()) }}</h1>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <th class="no-print">Datum</th>
                    @foreach ($cities as $city)
                        <th>{{ $city->name }}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach ($days as $day)
                    <tr>
                        @include('calendar.partials.dayheader')
                        @foreach($cities as $city)
                            <td class="@if($day->date->format('Ymd')==$nextDay->date->format('Ymd')) now @endif
                            @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) limited collapsed @endif
                            @if($day->day_type == \App\Day::DAY_TYPE_LIMITED && (count($day->cities->intersect(Auth::user()->cities))==0)) not-for-city @endif
                            @if($day->day_type == \App\Day::DAY_TYPE_LIMITED && ($day->cities->contains($city))) for-city @endif
                            @if(!Auth::user()->cities->contains($city)) not-my-city @endif
                                    "
                                @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) title="{{ $day->date->format('d.m.Y') }} (Klicken, um Ansicht umzuschalten)"
                                @endif
                                data-day="{{ $day->id }}"
                            >
                                <div class="celldata">
                                    @foreach ($services[$city->id][$day->id] as $service)
                                        @include('calendar.partials.service')
                                    @endforeach
                                    @include('calendar.partials.addbutton')
                                </div>
                            </td>
                        @endforeach
                    </tr>


                    @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
        <hr/>
    </div>
@endcomponent
