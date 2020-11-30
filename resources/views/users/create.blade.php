@extends('layouts.app')

@section('title', 'Benutzer hinzufügen')

@section('content')
    <form method="post" action="{{ route('users.store') }}" enctype="multipart/form-data">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            @endslot

            @tabheaders
                @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => true]) @endtabheader
                @tabheader(['id' => 'contact', 'title' => 'Kontaktdaten']) @endtabheader
                @tabheader(['id' => 'permissions', 'title' => 'Berechtigungen']) @endtabheader
                @tabheader(['id' => 'absences', 'title' => 'Urlaub']) @endtabheader
            @endtabheaders

            @tabs
                @tab(['id' => 'home', 'active' => true])
                    @input(['name' => 'name', 'label' => 'Name'])
                    @input(['name' => 'email', 'label' => 'E-Mailadresse'])
                    @input(['name' => 'password', 'label' => 'Passwort', 'type' => 'password'])
                    @input(['name' => 'title', 'label' => 'Titel'])
                    @input(['name' => 'first_name', 'label' => 'Vorname'])
                    @input(['name' => 'last_name', 'label' => 'Nachname'])
                    @upload(['name' => 'image', 'label' => 'Bild'])
                    @selectize(['name' => 'homeCities[]', 'label' => 'Dieser Benutzer gehört zu folgenden Kirchengemeinden', 'items' => Auth::user()->adminCities])
                    @selectize(['name' => 'parishes[]', 'label' => 'Dieser Benutzer hat folgende Pfarrämter inne', 'items' => $parishes])
                    @selectize(['name' => 'roles[]', 'label' => 'Benutzerrollen', 'items' => $roles])
                    <div class="form-group">
                        <label for="homescreen">Erster Bildschirm nach der Anmeldung</label>
                        <select class="form-control" name="homescreen">
                            <option value="route:calendar" selected>Kalender</option>
                            <option value="homescreen:configurable">Konfigurierbare Startseite</option>
                            <option value="homescreen:admin">Zusammenfassung für Administrator*innen</option>
                        </select>
                    </div>
                @endtab
                @tab(['id' => 'contact'])
                    @input(['name' => 'office', 'label' => 'Dienststelle'])
                    @textarea(['name' => 'address', 'label' => 'Adresse'])
                    @input(['name' => 'phone', 'label' => 'Telefon'])
                @endtab
                @tab(['id' => 'permissions'])
                <div class="form-group">
                    <label class="control-label">Zugriff auf Kirchengemeinden:</label>
                    @foreach ($cities as $city)
                        @if($city->administeredBy(Auth::user()))
                        <div class="form-group row" data-city="{{ $city->id }}">
                            <label class="col-sm-3">{{ $city->name }}</label>
                            <div class="col-sm-9">
                                <select class="form-control check-city"
                                        name="cityPermission[{{ $city->id }}][permission]" data-city="{{ $city->id }}"  style="color: white;">
                                    <option value="a" data-city-write="{{ $city->id }}" style="background-color: purple; color: white;">
                                        Administrator
                                    </option>
                                    <option value="w" data-city-write="{{ $city->id }}" style="background-color: green">
                                        Schreibzugriff
                                    </option>
                                    <option value="r" data-city-read="{{ $city->id }}" style="background-color: orange">
                                        Lesezugriff
                                    </option>
                                    <option value="n" selected data-city="{{ $city->id }}"
                                            style="background-color: red">Kein Zugriff
                                    </option>
                                </select>
                            </div>
                        </div>
                        @endif
                    @endforeach
                    <div class="form-group">
                        <label>Benutzer wird bei Änderungen an Gottesdiensten per E-Mail benachrichtigt für:</label>
                    </div>
                    @foreach ($cities as $city)
                        @if($city->administeredBy(Auth::user()))
                        <div class="form-group row city-subscription-row" data-city="{{ $city->id }}">
                            <label class="col-sm-2">{{ $city->name }}</label>
                            <div class="col-sm-10">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]"
                                           value="2"/>
                                    <label class="form-check-label" for="subscribe[{{ $city->id }}]">alle
                                        Gottesdienste</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]" value="1"
                                           checked/>
                                    <label class="form-check-label" for="subscribe[{{ $city->id }}]">eigene
                                        Gottesdienste</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="subscribe[{{ $city->id }}]"
                                           value="0"/>
                                    <label class="form-check-label" for="subscribe[{{ $city->id }}]">keine
                                        Gottesdienste</label>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
                @endtab
                @tab(['id' => 'absences'])
                    @checkbox(['name' => 'manage_absences', 'label' => 'Urlaub für diesen Benutzer verwalten'])
                    @peopleselect(['name' => 'approvers[]', 'label' => 'Urlaub muss durch folgende Personen genehmigt werden:', 'people' => $users])
                @endtab
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
            toggleSubscriptionRows();
            $('.check-city').change(function () {
                toggleSubscriptionRows();
            });
        });
    </script>
@endsection
