@extends('layouts.app')

@section('title', 'Pfarramt anlegen')

@section('content')
    @component('components.container')
    <div class="card">
        <div class="card-header">
            Pfarramt hinzufügen
        </div>
        <div class="card-body">
            @component('components.errors')
            @endcomponent
            <form method="post" action="{{ route('parishes.store') }}">
                <div class="form-group">
                    <label for="owningCity">Kirchengemeinde:</label>
                    <select class="form-control" name="owningCity">
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    @csrf
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name"/>
                </div>
                <div class="form-group">
                    <label for="code">Bezeichnung in DaviP:</label>
                    <input type="text" class="form-control" name="code"/>
                </div>
                <div class="form-group">
                    <label for="adress">Straße:</label>
                    <input type="text" class="form-control" name="address"/>
                </div>
                <div class="form-group">
                    <label for="zip">PLZ:</label>
                    <input type="text" class="form-control" name="zip"/>
                </div>
                <div class="form-group">
                    <label for="city">Ort:</label>
                    <input type="text" class="form-control" name="city"/>
                </div>
                <div class="form-group">
                    <label for="phone">Telefon:</label>
                    <input type="text" class="form-control" name="phone"/>
                </div>
                <div class="form-group">
                    <label for="email">E-Mailadresse:</label>
                    <input type="text" class="form-control" name="email"/>
                </div>
                <hr />
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function($){
            $('#name').focus();
        });
    </script>
    @endcomponent
@endsection
