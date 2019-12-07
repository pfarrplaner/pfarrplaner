@extends('layouts.app')

@section('title', 'Trauung hinzufügen : Schritt 1')

@section('content')
    <form method="post" action="{{ route('weddings.wizard.step2') }}" enctype="multipart/form-data">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary" id="submit">Weiter &gt;</button>
            @endslot
            <div class="form-group">
                <label class="control-label" for="city">Kirchengemeinde:</label>
                <select class="form-control" name="city">
                    @foreach ($cities as $city)
                        <option value="{{ $city->id }}">
                            {{$city->name}}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="date">Datum</label>
                <input type="text" class="form-control datepicker" name="date" placeholder="tt.mm.jjjj"/>
            </div>
        @endcomponent
    </form>
@endsection
