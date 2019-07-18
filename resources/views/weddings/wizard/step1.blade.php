@extends('layouts.app')

@section('title', 'Bestattung hinzufügen : Schritt 1')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Trauung hinzufügen : Schritt 1
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('weddings.wizard.step2') }}" enctype="multipart/form-data">
                    @csrf
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
                        <input type="text" class="form-control datepicker" name="date" placeholder="tt.mm.jjjj" />
                    </div>
                    <button type="submit" class="btn btn-primary" id="submit">Weiter &gt;</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
