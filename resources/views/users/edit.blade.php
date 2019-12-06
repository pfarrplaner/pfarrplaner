@extends('layouts.app')

@section('title', 'Benutzer bearbeiten')


@section('content')
    <form method="post" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
        <div class="card">
            <div class="card-body">
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
                    <hr/>
                    <div class="form-group">
                        <label for="title">Titel:</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ $user->title }}"/>
                    </div>
                    <div class="form-group">
                        <label for="first_name">Vorname:</label>
                        <input type="text" class="form-control" id="first_name" name="first_name"
                               value="{{ $user->first_name }}"/>
                    </div>
                    <div class="form-group">
                        <label for="last_name">Nachname:</label>
                        <input type="text" class="form-control" id="last_name" name="last_name"
                               value="{{ $user->last_name }}"/>
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
                    <div class="form-group">
                        <label for="image">Bild:</label>
                        @if ($user->image)
                            <a id="linkToAttachment"
                               href="{{ env('APP_URL').'storage/'.$user->image }}">{{ $user->image }}</a>
                            <a class="btn btn-sm btn-danger" id="btnRemoveAttachment" title="Bild entfernen"><span
                                        class="fa fa-trash"></span></a>
                        @else
                            <input type="file" class="form-control" id="image" name="image"/>
                        @endif
                    </div>
                    <div class="form-group"> <!-- Radio group !-->
                        <label class="control-label">Zugriff auf Kirchengemeinden:</label>
                        @foreach ($cities as $city)
                            <div class="form-group row" data-city="{{ $city->id }}">
                                <label class="col-sm-3">{{ $city->name }}</label>
                                <div class="col-sm-9">
                                    <select class="form-control check-city"
                                            name="cityPermission[{{ $city->id }}][permission]"
                                            data-city="{{ $city->id }}">
                                        <option value="w" @if($user->writableCities->contains($city)) selected
                                                @endif data-city-write="{{ $city->id }}"
                                                style="background-color: green">Schreibzugriff
                                        </option>
                                        <option value="r"
                                                @if($user->visibleCities->contains($city) && !$user->writableCities->contains($city)) selected
                                                @endif data-city-read="{{ $city->id }}"
                                                style="background-color: yellow">Lesezugriff
                                        </option>
                                        <option value="n" @if(!$user->visibleCities->contains($city)) selected
                                                @endif data-city="{{ $city->id }}" style="background-color: red">Kein
                                            Zugriff
                                        </option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <label>Benutzer wird bei Änderungen an Gottesdiensten per E-Mail benachrichtigt für:</label>
                    </div>
                    @foreach ($cities as $city)
                        <div class="form-group row city-subscription-row" data-city="{{ $city->id }}">
                            <label class="col-sm-3">{{ $city->name }}</label>
                            <div class="col-sm-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]"
                                           value="2"
                                           @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_ALL) checked @endif>
                                    <label class="form-check-label" for="subscribe[{{ $city->id }}]">alle
                                        Gottesdienste</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]"
                                           value="1"
                                           @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_OWN) checked @endif>
                                    <label class="form-check-label" for="subscribe[{{ $city->id }}]">eigene
                                        Gottesdienste</label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]"
                                           value="0"
                                           @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_NONE) checked @endif>
                                    <label class="form-check-label" for="subscribe[{{ $city->id }}]">keine
                                        Gottesdienste</label>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    <hr/>
                    <div class="form-group">
                        <label for="homeCities">Dieser Benutzer gehört zu folgenden Kirchengemeinden:</label>
                        <select class="form-control fancy-selectize" name="homeCities[]" multiple>
                            <option></option>
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}"
                                        @if($user->homeCities->contains($city)) selected @endif>{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="parishes">Dieser Benutzer hat folgende Pfarrämter inne:</label>
                        <select class="form-control fancy-selectize" name="parishes[]" multiple>
                            <option></option>
                            @foreach ($parishes as $parish)
                                <option value="{{ $parish->id }}"
                                        @if ($user->parishes->contains($parish)) selected @endif>{{ $parish->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="roles[]">Benutzerrollen</label>
                        <select class="form-control fancy-selectize" name="roles[]" multiple>
                            @foreach($roles as $role)
                                <option @if($user->roles->contains($role)) selected @endif>{{ $role->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <label for="homescreen">Erster Bildschirm nach der Anmeldung</label>
                        <select class="form-control" name="homescreen">
                            <option value="route:calendar" @if($homescreen == 'route:calendar')selected @endif>
                                Kalender
                            </option>
                            <option value="homescreen:pastor" @if($homescreen == 'homescreen:pastor')selected @endif>
                                Zusammenfassung für Pfarrer*in
                            </option>
                            <option value="homescreen:ministry"
                                    @if($homescreen == 'homescreen:ministry')selected @endif>Zusammenfassung für andere
                                Beteiligte
                            </option>
                            <option value="homescreen:secretary"
                                    @if($homescreen == 'homescreen:secretary')selected @endif>Zusammenfassung für
                                Sekretär*in
                            </option>
                            <option value="homescreen:office" @if($homescreen == 'homescreen:office')selected @endif>
                                Zusammenfassung für Kirchenpflege/Kirchenregisteramt
                            </option>
                            <option value="homescreen:admin" @if($homescreen == 'homescreen:admin')selected @endif>
                                Zusammenfassung für Administrator*innen
                            </option>
                        </select>
                    </div>
                    <hr/>
                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" name="manage_absences" value="1"
                                   @if($user->manage_absences) checked @endif/>
                            <label class="form-check-label" for="manage_absences">
                                Urlaub für diesen Benutzer verwalten
                            </label>
                        </div>
                    </div>
            </div>
            <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Speichern</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        function toggleSubscriptionRows() {
            $('.city-subscription-row').each(function () {
                var value = $('select[data-city=' + $(this).data('city') + ']').val();
                if (value == 'r' | value == 'w') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
                var color = '';
                switch (value) {
                    case 'w':
                        color = 'green';
                        break;
                    case 'r':
                        color = 'yellow';
                        break;
                    case 'n':
                        color = 'red';
                        break;
                }
                $('select[data-city=' + $(this).data('city') + ']').css('background-color', color);
            });
        }

        $(document).ready(function () {

            $('#btnRemoveAttachment').click(function () {
                $('#linkToAttachment').after('<input type="file" name="image" class="form-control" /><input type="hidden" name="removeAttachment" value="1" />');
                $('#linkToAttachment').hide();
                $('#btnRemoveAttachment').hide();
            });


            toggleSubscriptionRows();
            $('.check-city').change(function () {
                toggleSubscriptionRows();
            });
        });
    </script>
@endsection