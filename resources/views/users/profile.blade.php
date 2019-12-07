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
            </div>
        @endcomponent
    </form>
@endsection

@section('scripts')
@endsection
