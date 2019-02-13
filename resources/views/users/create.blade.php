@extends('layouts.app')

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
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Darf folgende Bereiche bearbeiten:</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="isAdmin" value="1"
                               id="isAdmin">
                        <label class="form-check-label" for="isAdmin">
                            <b>Alles</b> (Administrator)
                        </label>
                    </div>
                    @foreach (['church' => 'Kirche', 'general' => 'Allgemeine Gottesdienstfelder'] as $key => $title)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="canEdit{{ ucfirst($key) }}" value="1"
                                   id="canEdit{{ ucfirst($key) }}">
                            <label class="form-check-label" for="canEdit{{ ucfirst($key) }}">
                                {{ $title }}
                            </label>
                        </div>
                    @endforeach
                    @foreach (['pastor' => 'Pfarrer', 'organist' => 'Organist', 'sacristan' => 'Mesner'] as $key => $title)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="canEditField[{{ $key }}]" value="{{$key}}"
                                   id="canEditField{{ ucfirst($key) }}">
                            <label class="form-check-label" for="canEditField{{ ucfirst($key) }}">
                                {{ $title }}
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
                <br />
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </form>
        </div>
    </div>
    @endcomponent
@endsection
