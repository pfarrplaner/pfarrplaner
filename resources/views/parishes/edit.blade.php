@extends('layouts.app')

@section('title', 'Pfarramt bearbeiten')

@section('content')
    <form method="post" action="{{ route('parishes.update', $parish) }}">
        @csrf
        @method('PATCH')
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Speichern</button>
            @endslot
            <div class="form-group">
                <label for="owningCity">Kirchengemeinde:</label>
                <select class="form-control" name="owningCity">
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}"
                                @if($city->id == $parish->city_id) selected @endif>{{ $city->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $parish->name }}"/>
            </div>
            <div class="form-group">
                <label for="code">Bezeichnung in DaviP:</label>
                <input type="text" class="form-control" name="code" value="{{ $parish->code }}"/>
            </div>
            <div class="form-group">
                <label for="congregation_name">Name der Teilkirchengemeinde</label>
                <input type="text" class="form-control" name="congregation_name"
                       placeholder="Leer lassen, wenn keine Teilkirchengemeinde"
                       value="{{ $parish->congregation_name }}"/>
            </div>
            <div class="form-group">
                <label for="congregation_url">Link zur Teilkirchengemeinde</label>
                <input type="text" class="form-control" name="congregation_url" value="{{ $parish->congregation_url }}"
                       placeholder="Leer lassen, wenn keine Teilkirchengemeinde"/>
            </div>
            <div class="form-group">
                <label for="adress">Straße:</label>
                <input type="text" class="form-control" name="address" value="{{ $parish->address }}"/>
            </div>
            <div class="form-group">
                <label for="zip">PLZ:</label>
                <input type="text" class="form-control" name="zip" value="{{ $parish->zip }}"/>
            </div>
            <div class="form-group">
                <label for="city">Ort:</label>
                <input type="text" class="form-control" name="city" value="{{ $parish->city }}"/>
            </div>
            <div class="form-group">
                <label for="phone">Telefon:</label>
                <input type="text" class="form-control" name="phone" value="{{ $parish->phone }}"/>
            </div>
            <div class="form-group">
                <label for="email">E-Mailadresse:</label>
                <input type="text" class="form-control" name="email" value="{{ $parish->email }}"/>
            </div>
            <hr/>
            <div class="form-group">
                <label for="csv">CSV-formatierte Straßeneinträge aus DaviP</label>
                <textarea rows="15" class="form-control" name="csv"></textarea>
            </div>
        @endcomponent
    </form>
@endsection


@section('scripts')
    <script>
        $(document).ready(function ($) {
            $('#name').focus();
        });
    </script>
@endsection
