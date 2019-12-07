@extends('layouts.app')

@section('title', 'Mein Profil')


@section('content')
    <form method="post" action="{{ route('user.profile.save') }}">
        @csrf
        @method('PATCH')
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Speichern</button>
            @endslot

            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" href="#profile" role="tab" data-toggle="tab">Profil</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#view" role="tab" data-toggle="tab">Anzeige</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#notifications" role="tab"
                       data-toggle="tab">Benachrichtigungen</a>
                </li>
            </ul>

            <div class="tab-content" style="padding-top: 15px;">
                <div role="tabpanel" class="tab-pane fade in active show" id="profile">

                    <div class="form-group">
                        <label for="email">E-Mailadresse:</label>
                        <input type="text" class="form-control" id="email" name="email"
                               value="{{ $user->email }}"/>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="office">Pfarramt/Büro:</label>
                        <input type="text" class="form-control" id="office" name="office"
                               value="{{ $user->office }}"/>
                    </div>
                    <div class="form-group">
                        <label for="address">Adresse:</label>
                        <textarea name="address" class="form-control">{{ $user->address }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="phone">Telefon:</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                               value="{{ $user->phone }}"/>
                    </div>
                </div>
                <div role="tabpanel" class="tab-pane fade" id="notifications">

                    <div class="form-group">
                        <label>Benachrichtige mich per E-Mail bei Änderungen an Gottesdiensten für:</label>
                    </div>
                    @foreach ($cities as $city)
                        <div class="form-group row city-subscription-row" data-city="{{ $city->id }}">
                            <label class="col-sm-2">{{ $city->name }}</label>
                            <div class="col-sm-10">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="subscribe[{{ $city->id }}]" value="2"
                                           @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_ALL) checked @endif>
                                    <label class="form-check-label" for="subscribe[{{ $city->id }}]">alle
                                        Gottesdienste</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="subscribe[{{ $city->id }}]" value="1"
                                           @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_OWN) checked @endif>
                                    <label class="form-check-label" for="subscribe[{{ $city->id }}]">eigene
                                        Gottesdienste</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio"
                                           name="subscribe[{{ $city->id }}]" value="0"
                                           @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_NONE) checked @endif>
                                    <label class="form-check-label" for="subscribe[{{ $city->id }}]">keine
                                        Gottesdienste</label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div role="tabpanel" class="tab-pane fade" id="view">
                    <label>Kirchengemeinden anzeigen</label>
                    <p>Hier kannst du festlegen, welche Kirchengemeinden in welcher Reihenfolge im Kalender
                        angezeigt werden sollen. Schiebe dazu einfach die einzelnen Gemeindenamen mit der Maus
                        an die gewünschte Stelle.</p>
                    <div class="row">
                        <div class="col-sm-6">
                            <label for="userCities">Anzeigen</label>
                            <ul id="userCities" class="sortable citySort">
                                @foreach($sortedCities as $city)
                                    <li data-city="{{ $city->id }}"><span
                                                class="fa fa-church"></span> {{ $city->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-sm-6">
                            <label for="unusedCities">Nicht anzeigen</label>
                            <ul id="unusedCities" class="sortable citySort">
                                @foreach($unusedCities as $city)
                                    <li data-city="{{ $city->id }}"><span
                                                class="fa fa-church"></span> {{ $city->name }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <input type="hidden" name="citySort" value=""/>

                    <hr/>

                    <label>Kalenderansicht</label>
                    <div class="row">
                        <div class="col-6">
                            <label>
                                <input type="radio" name="calendar_view" value="calendar.month"
                                       @if($calendarView == 'calendar.month')checked @endif/>
                                <div style="border: solid 1px #dddddd; padding: 3px; border-radius: 3px; display: inline-block;">
                                    <b>Horizontal</b><br/>Tage in Spalten, Kirchengemeinden in Zeilen
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th><span class="fa fa-calendar-day"></span></th>
                                                <th><span class="fa fa-calendar-day"></span></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th><span class="fa fa-church"></span></th>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <th><span class="fa fa-church"></span></th>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </label>
                        </div>
                        <div class="col-6">
                            <label>
                                <input type="radio" name="calendar_view" value="calendar.month_vertical"
                                       @if($calendarView == 'calendar.month_vertical')checked @endif/>
                                <div style="border: solid 1px #dddddd; padding: 3px; border-radius: 3px; display: inline-block;">
                                    <b>Vertikal</b><br/>Kirchengemeinden in Spalten, Tage in Zeilen
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th><span class="fa fa-church"></span></th>
                                                <th><span class="fa fa-church"></span></th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <th><span class="fa fa-calendar-day"></span></th>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            <tr>
                                                <th><span class="fa fa-calendar-day"></span></th>
                                                <td>&nbsp;</td>
                                                <td>&nbsp;</td>
                                            </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                </div>
            </div>
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-sortable/0.9.13/jquery-sortable-min.js"></script>
    <script>
        function setCitySortValue() {
            var value = [];
            $('#userCities li').each(function () {
                if ($(this).data('city')) value.push($(this).data('city'));
            });
            $('input[name=citySort]').val(value.join(','));
        }

        $(document).ready(function () {
            setCitySortValue();
        });

        $('.citySort').sortable({
            group: 'citySort',
            pullPlaceholder: false,
            // animation on drop
            onDrop: function ($item, container, _super) {
                _super($item, container);
                setCitySortValue();
            },

            // set $item relative to cursor position
            onDragStart: function ($item, container, _super) {
                var offset = $item.offset(),
                    pointer = container.rootGroup.pointer;

                adjustment = {
                    left: pointer.left - offset.left,
                    top: pointer.top - offset.top
                };

                _super($item, container);
            },
            onDrag: function ($item, position) {
                $item.css({
                    left: position.left - adjustment.left,
                    top: position.top - adjustment.top
                });
            }
        });
    </script>
@endsection
