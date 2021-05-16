@extends('layouts.app')

@section('title', 'Mein Profil')


@section('content')
    <div class="row">
        <div class="col-md-8">
            <form method="post" action="{{ route('user.profile.save') }}">
                @csrf
                @method('PATCH')
                @component('components.ui.card')
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary">Speichern</button>
                    @endslot


                    @tabheaders
                        @tabheader(['id' => 'profile', 'title' => 'Profil', 'active' => $tab == '']) @endtabheader
                        @tabheader(['id' => 'notifications', 'title' => 'Benachrichtigungen', 'active' => $tab == 'notifications']) @endtabheader
                        @tabheader(['id' => 'homescreen', 'title' => 'Startbildschirm', 'active' => $tab == 'homescreen']) @endtabheader
                        @if (strpos($user->email, '@elkw.de') !== false)
                            @tabheader(['id' => 'calendars', 'title' => 'Verbundene Kalender', 'active' => $tab == 'calendars']) @endtabheader
                        @endif
                    @endtabheaders

                    @tabs
                        @tab(['id' => 'profile', 'active' => $tab == ''])
                            @input(['label' => 'Name', 'name' => 'name', 'id' => 'email', 'value' => $user->name])
                            @input(['label' => 'E-Mailadresse', 'name' => 'email', 'id' => 'email', 'value' => $user->email])
                            <hr/>
                            @input(['label' => 'Pfarramt/Büro', 'name' => 'office', 'value' => $user->office])
                            @textarea(['label' => 'Adresse', 'name' => 'address', 'value' => $user->address])
                            @input(['label' => 'Telefon', 'name' => 'phone', 'value' => $user->phone])
                        @endtab
                        @tab(['id' => 'notifications', 'active' => $tab == 'notifications'])
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
                                                   name="subscribe[{{ $city->id }}]" value="4"
                                                   @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_TIME_CHANGES) checked @endif>
                                            <label class="form-check-label" for="subscribe[{{ $city->id }}]">nur bei Zeit-/Datumsänderungen</label>
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
                        @endtab
                        @include('users.profile.homescreen')
                        @if (strpos($user->email, '@elkw.de') !== false)
                            @tab(['id' => 'calendars', 'active' => $tab == 'calendars'])
                                <p>Wenn du ein elkw.de-Konto hast, kannst du hier Kalender aus deinem Outlook oder auf dem Sharepoint verbinden.
                                    Diese werden dann automatisch mit den hier angelegten Gottesdiensten befüllt.</p>
                                @if(count($user->calendarConnections) == 0)
                                    <p>Aktuell sind noch keine externen Kalender verbunden.</p>
                                @else
                                    @foreach($user->calendarConnections as $calendarConnection)
                                    @endforeach
                                @endif
                                <a class="btn btn-success" href="{{ route('calendarConnection.create') }}"><span class="fa fa-plus"></span> Kalender verbinden</a>
                            @endtab
                        @endif
                    @endtabs

                @endcomponent
            </form>

        </div>
        <div class="col-md-4">
            <form method="post" action="{{ route('password.edit') }}">
                @csrf
                @component('components.ui.card')
                    @slot('cardHeader')Passwort ändern @endslot
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary">Passwort ändern</button>
                    @endslot
                    @input(['name' => 'current_password', 'label' => 'Aktuelles Passwort', 'type' => 'password'])
                    @input(['name' => 'new_password', 'label' => 'Neues Passwort', 'type' => 'password'])
                    @input(['name' => 'new_password_confirmation', 'label' => 'Neues Passwort wiederholen', 'type' => 'password'])
                @endcomponent
            </form>

        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js"></script>
    <script src="{{ asset('js/pfarrplaner/profile-homescreen.js') }}"></script>
@endsection
