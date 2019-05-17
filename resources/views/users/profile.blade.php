@extends('layouts.app')

@section('title', 'Mein Profil')


@section('content')
    @component('components.container')
    <div class="card">
        <div class="card-header">
            Mein Profil
        </div>
        <div class="card-body">
            @component('components.errors')
            @endcomponent
            <form method="post" action="{{ route('user.profile.save') }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="email">E-Mailadresse:</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}"/>
                </div>
                <hr />
                <div class="form-group">
                    <label for="office">Pfarramt/Büro:</label>
                    <input type="text" class="form-control" id="office" name="office" value="{{ $user->office }}"/>
                </div>
                <div class="form-group">
                    <label for="address">Adresse:</label>
                    <textarea name="address" class="form-control">{{ $user->address }}</textarea>
                </div>
                <div class="form-group">
                    <label for="phone">Telefon:</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ $user->phone }}"/>
                </div>
                <div class="form-group">
                    <label>Benachrichtige mich per E-Mail bei Änderungen an Gottesdiensten für:</label>
                </div>
                @foreach ($cities as $city)
                <div class="form-group row city-subscription-row" data-city="{{ $city->id }}">
                    <label class="col-sm-2">{{ $city->name }}</label>
                    <div class="col-sm-10">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]" value="2" @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_ALL) checked @endif>
                            <label class="form-check-label" for="subscribe[{{ $city->id }}]" >alle Gottesdienste</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]" value="1" @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_OWN) checked @endif>
                            <label class="form-check-label" for="subscribe[{{ $city->id }}]">eigene Gottesdienste</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]" value="0" @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_NONE) checked @endif>
                            <label class="form-check-label" for="subscribe[{{ $city->id }}]">keine Gottesdienste</label>
                        </div>
                    </div>
                </div>
                @endforeach
                <hr />
                <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>
    @endcomponent
@endsection
