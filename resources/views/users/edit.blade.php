@extends('layouts.app')

@section('title', 'Benutzer bearbeiten')


@section('content')
    <form method="post" action="{{ route('users.update', $user->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Speichern</button>
            @endslot

            @tabheaders
                @if ($user->administeredBy(Auth::user()))
                    @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => $user->administeredBy(Auth::user())]) @endtabheader
                    @tabheader(['id' => 'contact', 'title' => 'Kontaktdaten', 'active' => !$user->administeredBy(Auth::user())]) @endtabheader
                    @tabheader(['id' => 'permissions', 'title' => 'Berechtigungen']) @endtabheader
                    @tabheader(['id' => 'absences', 'title' => 'Urlaub']) @endtabheader
                @else
                    @tabheader(['id' => 'permissions', 'title' => 'Berechtigungen', 'active' => true]) @endtabheader
                @endif
            @endtabheaders

            @tabs
                @if ($user->administeredBy(Auth::user()))
                    @tab(['id' => 'home', 'active' => true])
                        @input(['name' => 'name', 'label' => 'Name', 'value' => $user->name])
                        @input(['name' => 'email', 'label' => 'E-Mailadresse', 'value' => $user->email])
                        @input(['name' => 'password', 'label' => 'Passwort', 'type' => 'password', 'placeholder' => ($user->password != '' ? 'Nur eingeben, wenn Passwort geändert werden soll' : 'Passwort setzen, um diese Person als Benutzer anzulegen')])
                        @input(['name' => 'title', 'label' => 'Titel', 'value' => $user->title])
                        @input(['name' => 'first_name', 'label' => 'Vorname', 'value' => $user->first_name])
                        @input(['name' => 'last_name', 'label' => 'Nachname', 'value' => $user->last_name])
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
                        @selectize(['name' => 'homeCities[]', 'label' => 'Dieser Benutzer gehört zu folgenden Kirchengemeinden', 'items' => Auth::user()->adminCities, 'value' => $user->homeCities])
                        @selectize(['name' => 'parishes[]', 'label' => 'Dieser Benutzer hat folgende Pfarrämter inne', 'items' => $parishes, 'value' => $user->parishes])
                        @selectize(['name' => 'roles[]', 'label' => 'Benutzerrollen', 'items' => $roles, 'value' => $user->roles])
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
                    @endtab
                @endif
                @tab(['id' => 'contact', 'active' => !$user->administeredBy(Auth::user())])
                    @input(['name' => 'office', 'label' => 'Dienststelle', 'value' => $user->office])
                    @textarea(['name' => 'address', 'label' => 'Adresse', 'value' => $user->address])
                    @input(['name' => 'phone', 'label' => 'Telefon', 'value' => $user->phone])
                @endtab
                @tab(['id' => 'permissions'])
                @if ((!$user->administeredBy(Auth::user())) && ($user->password == ''))
                    @input(['name' => 'password', 'label' => 'Passwort', 'type' => 'password', 'placeholder' => ($user->password != '' ? 'Nur eingeben, wenn Passwort geändert werden soll' : 'Passwort setzen, um diese Person als Benutzer anzulegen')])
                @endif
                <div class="form-group">
                    <label class="control-label">Zugriff auf Kirchengemeinden:</label>
                    @foreach ($cities as $city)
                        @if($city->administeredBy(Auth::user()))
                            <div class="form-group row" data-city="{{ $city->id }}">
                                <label class="col-sm-3">{{ $city->name }}</label>
                                <div class="col-sm-9">
                                    <select class="form-control check-city"
                                            name="cityPermission[{{ $city->id }}][permission]"
                                            data-city="{{ $city->id }}" style="color: white;">
                                        <option value="a" @if($user->permissionForCity($city) == 'a') selected
                                                @endif data-city-write="{{ $city->id }}"
                                                style="background-color: purple; color: white;">Administrator
                                        </option>
                                        <option value="w" @if($user->permissionForCity($city) == 'w') selected
                                                @endif data-city-write="{{ $city->id }}"
                                                style="background-color: green">Schreibzugriff
                                        </option>
                                        <option value="r"
                                                @if($user->permissionForCity($city) == 'r') selected
                                                @endif data-city-read="{{ $city->id }}"
                                                style="background-color: orange">Lesezugriff
                                        </option>
                                        <option value="n" @if($user->permissionForCity($city) == 'n') selected
                                                @endif data-city="{{ $city->id }}" style="background-color: red">Kein
                                            Zugriff
                                        </option>
                                    </select>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
                <div class="form-group">
                    <label>Benutzer wird bei Änderungen an Gottesdiensten per E-Mail benachrichtigt für:</label>
                </div>
                @foreach ($cities as $city)
                    @if($city->administeredBy(Auth::user()))
                    <div class="form-group row city-subscription-row" data-city="{{ $city->id }}">
                        <label class="col-sm-3">{{ $city->name }}</label>
                        <div class="col-sm-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]"
                                       value="2"
                                       @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_ALL) checked
                                       @endif>
                                <label class="form-check-label" for="subscribe[{{ $city->id }}]">alle
                                    Gottesdienste</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]"
                                       value="1"
                                       @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_OWN) checked
                                       @endif>
                                <label class="form-check-label" for="subscribe[{{ $city->id }}]">eigene
                                    Gottesdienste</label>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]"
                                       value="0"
                                       @if($user->getSubscriptionType($city) == \App\Subscription::SUBSCRIBE_NONE) checked
                                       @endif>
                                <label class="form-check-label" for="subscribe[{{ $city->id }}]">keine
                                    Gottesdienste</label>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            @endtab
                @if ($user->administeredBy(Auth::user()))
                    @tab(['id' => 'absences'])
                        @checkbox(['name' => 'manage_absences', 'label' => 'Urlaub für diesen Benutzer verwalten', 'value' => $user->manage_absences])
                        @peopleselect(['name' => 'approvers[]', 'label' => 'Urlaub muss durch folgende Personen genehmigt werden:', 'people' => $users, 'value' => $user->approvers])
                    @endtab
                @endif
            @endtabs
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        function toggleSubscriptionRows() {
            $('.city-subscription-row').each(function () {
                var value = $('select[data-city=' + $(this).data('city') + ']').val();
                if (value == 'r' | value == 'w' | value == 'a') {
                    $(this).show();
                } else {
                    $(this).hide();
                }
                var color = '';
                switch (value) {
                    case 'a':
                        color = 'purple';
                        break;
                    case 'w':
                        color = 'green';
                        break;
                    case 'r':
                        color = 'orange';
                        break;
                    case 'n':
                        color = 'red';
                        break;
                }
                $('select[data-city=' + $(this).data('city') + ']').css('background-color', color);
            });
        }

        $(document).ready(function () {

            $('.peopleSelect').selectize({});


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
