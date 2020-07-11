@component('components.ui.loader')
    <div class="calendar-month calendar-horizontal">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="no-print" class="text-left" style="vertical-align: bottom;">
                    @if ($slave)
                        <div class="badge badge-info">
                            <span id="heartbeat" class="fa fa-sync"></span>
                            Auto-Update
                            <br/>
                        </div><br/>
                    @endif
                    Kirchengemeinde
                </th>
                @foreach ($days as $day)
                    @include('calendar.partials.dayheader')
                @endforeach
            </tr>
            </thead>
            <tbody>
            @foreach($cities as $city)
                <tr>
                    <td class="no-print">{{$city->name}}</td>
                    @foreach ($days as $day)
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
            </tbody>
        </table>
    </div>
@endcomponent
