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
                        @tabheader(['id' => 'profile', 'title' => 'Profile', 'active' => true]) @endtabheader
                        @tabheader(['id' => 'notifications', 'title' => 'Benachrichtigungen']) @endtabheader
                        @if(null !== $homeScreen)
                            @tabheader(['id' => 'homescreen', 'title' => 'Startbildschirm']) @endtabheader
                        @endif
                    @endtabheaders

                    @tabs
                        @tab(['id' => 'profile', 'active' => true])
                            @input(['label' => 'Name', 'name' => 'email', 'id' => 'email', 'value' => $user->email])
                            <hr/>
                            @input(['label' => 'Pfarramt/Büro', 'name' => 'office', 'value' => $user->office])
                            @textarea(['label' => 'Adresse', 'name' => 'address', 'value' => $user->address])
                            @input(['label' => 'Telefon', 'name' => 'phone', 'value' => $user->phone])
                            @input(['name' => 'api_token', 'label' => 'API-Token', 'value' => $user->api_token])
                        @endtab
                        @tab(['id' => 'notifications'])
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
                        @endtab
                        @if(null !== $homeScreen)
                            @tab(['id' => 'homescreen'])
                                {!! $homeScreen->renderConfigurationView() !!}
                            @endtab
                        @endif
                    @endtabs

                @endcomponent
            </form>

        </div>
        <div class="col-md-4">
            <form method="post" action="{{ route('changePassword') }}">
                @csrf
                @component('components.ui.card')
                    @slot('cardHeader')Passwort ändern @endslot
                    @slot('cardFooter')
                        <button type="submit" class="btn btn-primary">Passwort ändern</button>
                    @endslot
                    <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                        <label for="new-password" class="control-label">Aktuelles Passwort</label>
                        <input id="current-password" type="password" class="form-control" name="current-password"
                               required>
                        @if ($errors->has('current-password'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('current-password') }}</strong>
                                    </span>
                        @endif
                    </div>

                    <div class="form-group{{ $errors->has('new-password') ? ' has-error' : '' }}">
                        <label for="new-password" class="control-label">Neues Passwort</label>
                        <input id="new-password" type="password" class="form-control" name="new-password" required>
                        @if ($errors->has('new-password'))
                            <span class="help-block">
                                        <strong>{{ $errors->first('new-password') }}</strong>
                                    </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="new-password-confirm" class="control-label">Neues Passwort wiederholen</label>
                        <input id="new-password-confirm" type="password" class="form-control"
                               name="new-password_confirmation" required>
                    </div>
                @endcomponent
            </form>

        </div>
    </div>
@endsection

@section('scripts')
@endsection
