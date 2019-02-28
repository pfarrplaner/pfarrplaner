@extends('layouts.app')

@section('content')
    @component('components.container')
    <div class="card">
        <div class="card-header">
            Benutzer bearbeiten
        </div>
        <div class="card-body">
            @component('components.errors')
            @endcomponent
            <form method="post" action="{{ route('users.update', $user->id) }}">
                @csrf
                @method('PATCH')
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}"/>
                </div>
                <div class="form-group">
                    <label for="email">E-Mailadresse:</label>
                    <input type="text" class="form-control" id="email" name="email" value="{{ $user->email }}"/>
                </div>
                <div class="form-group">
                    <label for="password">Neues Passwort:</label>
                    <input type="text" class="form-control" id="password" name="password" value=""/>
                </div>
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
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Gehört zu folgenden Kirchengemeinden:</label>
                    @foreach ($cities as $city)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cities[]" value="{{ $city->id }}"
                                   id="defaultCheck{{$city->id}}" @if ($user->cities->contains($city)) checked @endif>
                            <label class="form-check-label" for="defaultCheck{{$city->id}}">
                                {{$city->name}}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Darf folgende Bereiche bearbeiten:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="isAdmin" value="1"
                               id="isAdmin" @if ($user->isAdmin) checked @endif>
                        <label class="form-check-label" for="isAdmin">
                            <b>Alles</b> (Administrator)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="canEditGeneral" value="1"
                               id="canEditGeneral" @if ($user->canEditGeneral) checked @endif>
                        <label class="form-check-label" for="canEditGeneral">
                            Allgemeine Gottesdienstfelder
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="canEditChurch" value="1"
                               id="canEditChurch" @if ($user->canEditChurch) checked @endif>
                        <label class="form-check-label" for="canEditChurch">
                            Kirche
                        </label>
                    </div>
                    @foreach (['pastor' => 'Pfarrer', 'organist' => 'Organist', 'sacristan' => 'Mesner'] as $key => $title)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="canEditField[{{ $key }}]" value="{{$key}}"
                                   id="canEditField{{ ucfirst($key) }}" @if ($user->canEditField($key)) checked @endif>
                            <label class="form-check-label" for="canEditField{{ ucfirst($key) }}">
                                {{ $title }}
                            </label>
                        </div>
                    @endforeach
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="canEditOfferings" value="1"
                               id="canEditOfferings" @if ($user->canEditOfferings) checked @endif>
                        <label class="form-check-label" for="canEditOfferings">
                            Opfer
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="canEditCC" value="1"
                               id="canEditCC" @if ($user->canEditCC) checked @endif>
                        <label class="form-check-label" for="canEditCC">
                            Kinderkirche
                        </label>
                    </div>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="notifications" value="1"
                           id="notifications" @if ($user->notifications) checked @endif>
                    <label class="form-check-label" for="notifications">
                        Dieser Benutzer wird per E-Mail benachrichtigt, wenn Gottesdienste seiner Gemeinde(n) geändert werden.
                    </label>
                </div>
                <br />
                <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>
    @endcomponent
@endsection
