@extends('layouts.app')

@section('title', 'Benutzer hinzufügen')

@section('content')
    @component('components.container')
    <div class="card">
        <div class="card-header">
            Benutzer hinzufügen
        </div>
        <div class="card-body">
            @component('components.errors')
            @endcomponent
            <form method="post" action="{{ route('users.store') }}">
                @csrf
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name"/>
                </div>
                <div class="form-group">
                    <label for="email">E-Mailadresse:</label>
                    <input type="text" class="form-control" id="email" name="email"/>
                </div>
                <div class="form-group">
                    <label for="password">Passwort:</label>
                    <input type="password" class="form-control" id="password" name="password"/>
                </div>
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Gehört zu folgenden Kirchengemeinden:</label>
                    @foreach ($cities as $city)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="cities[]" value="{{ $city->id }}"
                                   id="defaultCheck{{$city->id}}">
                            <label class="form-check-label" for="defaultCheck{{$city->id}}">
                                {{$city->name}}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="notifications" value="1"
                           id="notifications">
                    <label class="form-check-label" for="notifications">
                        Dieser Benutzer wird per E-Mail benachrichtigt, wenn Gottesdienste seiner Gemeinde(n) geändert werden.
                    </label>
                </div>
                <hr />
                <div class="form-group">
                    <label for="roles[]">Benutzerrollen</label>
                    <select class="form-control fancy-select2" name="roles[]" multiple>
                        @foreach($roles as $role)
                            <option>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <hr />
                <div class="form-group">
                    <label for="homescreen">Erster Bildschirm nach der Anmeldung</label>
                    <select class="form-control" name="homescreen">
                        <option value="route:calendar" selected>Kalender</option>
                        <option value="homescreen:pastor">Zusammenfassung für Pfarrer</option>
                        <option value="homescreen:ministry">Zusammenfassung für andere Beteiligte</option>
                        <option value="homescreen:secretary">Zusammenfassung für Sekretär</option>
                        <option value="homescreen:office">Zusammenfassung für Kirchenpflege/Kirchenregisteramt</option>
                    </select>
                </div>
                <hr />
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </form>
        </div>
    </div>
    @endcomponent
@endsection
