@extends('layouts.app')

@section('title', 'Benutzer bearbeiten')


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
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="notifications" value="1"
                           id="notifications" @if ($user->notifications) checked @endif>
                    <label class="form-check-label" for="notifications">
                        Dieser Benutzer wird per E-Mail benachrichtigt, wenn Gottesdienste seiner Gemeinde(n) geändert werden.
                    </label>
                </div>
                <hr />
                <div class="form-group">
                    <label for="roles[]">Benutzerrollen</label>
                    <select class="form-control fancy-select2" name="roles[]" multiple>
                        @foreach($roles as $role)
                            <option @if($user->roles->contains($role)) selected @endif>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <hr />
                <div class="form-group">
                    <label for="homescreen">Erster Bildschirm nach der Anmeldung</label>
                    <select class="form-control" name="homescreen">
                        <option value="route:calendar" @if($homescreen == 'route:calendar')selected @endif>Kalender</option>
                        <option value="homescreen:pastor" @if($homescreen == 'homescreen:pastor')selected @endif>Zusammenfassung für Pfarrer*in</option>
                        <option value="homescreen:ministry" @if($homescreen == 'homescreen:ministry')selected @endif>Zusammenfassung für andere Beteiligte</option>
                        <option value="homescreen:secretary" @if($homescreen == 'homescreen:secretary')selected @endif>Zusammenfassung für Sekretär*in</option>
                        <option value="homescreen:office" @if($homescreen == 'homescreen:office')selected @endif>Zusammenfassung für Kirchenpflege/Kirchenregisteramt</option>
                    </select>
                </div>
                <hr />
                <button type="submit" class="btn btn-primary">Speichern</button>
            </form>
        </div>
    </div>
    @endcomponent
@endsection
