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
                @tabheader(['id' => 'permissions', 'title' => 'Berechtigungen']) @endtabheader
                @tabheader(['id' => 'absences', 'title' => 'Urlaub']) @endtabheader
            @endtabheaders

            @tabs
                @tab(['id' => 'home', 'active' => true])
                    @input(['name' => 'name', 'label' => 'Name']) @endinput
                    @input(['name' => 'email', 'label' => 'E-Mailadresse']) @endinput
                    @input(['name' => 'password', 'label' => 'Passwort', 'type' => 'password']) @endinput
                    @input(['name' => 'title', 'label' => 'Titel']) @endinput
                    @input(['name' => 'first_name', 'label' => 'Vorname']) @endinput
                    @input(['name' => 'last_name', 'label' => 'Nachname']) @endinput
                    @upload(['name' => 'image', 'label' => 'Bild']) @endupload
                    @selectize(['name' => 'homeCities[]', 'label' => 'Dieser Benutzer gehört zu folgenden Kirchengemeinden', 'items' => $cities]) @endselectize
                    @selectize(['name' => 'parishes[]', 'label' => 'Dieser Benutzer hat folgende Pfarrämter inne', 'items' => $parishes]) @endselectize
                @endtab
                @tab(['id' => 'permissions'])
                <div class="form-group">
                    <label class="control-label">Zugriff auf Kirchengemeinden:</label>
                    @foreach ($cities as $city)
                        <div class="form-group row" data-city="{{ $city->id }}">
                            <label class="col-sm-3">{{ $city->name }}</label>
                            <div class="col-sm-9">
                                <select class="form-control check-city"
                                        name="cityPermission[{{ $city->id }}][permission]" data-city="{{ $city->id }}">
                                    <option value="w" data-city-write="{{ $city->id }}" style="background-color: green">
                                        Schreibzugriff
                                    </option>
                                    <option value="r" data-city-read="{{ $city->id }}" style="background-color: yellow">
                                        Lesezugriff
                                    </option>
                                    <option value="n" selected data-city="{{ $city->id }}"
                                            style="background-color: red">Kein Zugriff
                                    </option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                    <div class="form-group">
                        <label>Benutzer wird bei Änderungen an Gottesdiensten per E-Mail benachrichtigt für:</label>
                    </div>
                    @foreach ($cities as $city)
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
                    @endforeach
                    @selectize(['name' => 'roles[]', 'label' => 'Benutzerrollen', 'items' => $roles]) @endselectize
                    <div class="form-group">
                        <label for="homescreen">Erster Bildschirm nach der Anmeldung</label>
                        <select class="form-control" name="homescreen">
                            <option value="route:calendar" selected>Kalender</option>
                            <option value="homescreen:pastor">Zusammenfassung für Pfarrer</option>
                            <option value="homescreen:ministry">Zusammenfassung für andere Beteiligte</option>
                            <option value="homescreen:secretary">Zusammenfassung für Sekretär</option>
                            <option value="homescreen:office">Zusammenfassung für Kirchenpflege/Kirchenregisteramt</option>
                            <option value="homescreen:admin">Zusammenfassung für Administrator*innen</option>
                        </select>
                    </div>
                </div>
                @endtab
                @tab(['id' => 'absences'])
                    @checkbox(['name' => 'manage_absences', 'label' => 'Urlaub für diesen Benutzer verwalten']) @endcheckbox
                    @peopleselect(['name' => 'approvers[]', 'label' => 'Urlaub muss durch folgende Personen genehmigt werden:', 'people' => $users]) @endpeopleselect
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
            $('.peopleSelect').selectize({});
            toggleSubscriptionRows();
            $('.check-city').change(function () {
                toggleSubscriptionRows();
            });
        });
    </script>
@endsection
