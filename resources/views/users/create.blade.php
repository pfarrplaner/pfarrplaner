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
                <hr />
                <div class="form-group">
                    <label for="title">Titel:</label>
                    <input type="text" class="form-control" id="title" name="title" value=""/>
                </div>
                <div class="form-group">
                    <label for="first_name">Vorname:</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value=""/>
                </div>
                <div class="form-group">
                    <label for="last_name">Nachname:</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value=""/>
                </div>
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Gehört zu folgenden Kirchengemeinden:</label>
                    @foreach ($cities as $city)
                        <div class="form-check">
                            <input class="form-check-input check-city" data-city="{{ $city->id }}" type="checkbox" name="cities[]" value="{{ $city->id }}"
                                   id="defaultCheck{{$city->id}}">
                            <label class="form-check-label" for="defaultCheck{{$city->id}}">
                                {{$city->name}}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="form-group">
                    <label>Benutzer wird bei Änderungen an Gottesdiensten per E-Mail benachrichtigt für:</label>
                </div>
                @foreach ($cities as $city)
                    <div class="form-group row city-subscription-row" data-city="{{ $city->id }}">
                        <label class="col-sm-2">{{ $city->name }}</label>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]" value="2" />
                                <label class="form-check-label" for="subscribe[{{ $city->id }}]" >alle Gottesdienste</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]" value="1" checked />
                                <label class="form-check-label" for="subscribe[{{ $city->id }}]">eigene Gottesdienste</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]" value="0" />
                                <label class="form-check-label" for="subscribe[{{ $city->id }}]">keine Gottesdienste</label>
                            </div>
                        </div>
                    </div>
                @endforeach
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
    <script>
        function toggleSubscriptionRows() {
            $('.city-subscription-row').each(function(){
                if ($('input[data-city='+$(this).data('city')+']').prop('checked')) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        }

        $(document).ready(function(){
            toggleSubscriptionRows();
            $('.check-city').change(function(){
                toggleSubscriptionRows();
            });
        });
    </script>
    @endcomponent
@endsection
