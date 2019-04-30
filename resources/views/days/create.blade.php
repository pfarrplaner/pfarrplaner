@extends('layouts.app')

@section('title', 'Tag hinzufügen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Tag hinzufügen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('days.store') }}">
                    <div class="form-group">
                        @csrf
                        <label for="date">Datum</label>
                        <input type="text" class="form-control datepicker" name="date" placeholder="tt.mm.jjjj"
                               value="01.{{ str_pad($month, 2, 0, STR_PAD_LEFT) }}.{{ $year }}"/>
                    </div>
                    <div class="form-group">
                        <label for="name">Bezeichnung des Tages</label>
                        <input type="text" class="form-control" name="name"
                               placeholder="leer lassen für automatischen Eintrag"/>
                    </div>
                    <div class="form-group">
                        <label for="description">Hinweise zum Tag</label>
                        <textarea class="form-control" name="description"></textarea>
                    </div>
                    <div class="form-group">
                        <label style="display:block;">Anzeige</label>
                        <div class="form-check">
                            <input type="radio" name="day_type" value="{{ \App\Day::DAY_TYPE_DEFAULT }}"
                                   autocomplete="off" checked>
                            <label class="form-check-label">
                                Diesen Tag für alle Gemeinden anzeigen
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="day_type" value="{{ \App\Day::DAY_TYPE_LIMITED }}"
                                   autocomplete="off" id="check-type-limited">
                            <label class="form-check-label">
                                Diesen Tag nur für folgende Gemeinden anzeigen:
                            </label>
                            @foreach ($cities as $city)
                                <div class="form-check">
                                    <input class="form-check-input city-check @if(Auth::user()->cities->contains($city))my-city @else not-my-city @endif" type="checkbox" name="cities[]" value="{{ $city->id }}"
                                           id="defaultCheck{{$city->id}}">
                                    <label class="form-check-label" for="defaultCheck{{$city->id}}">
                                        {{$city->name}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="submit">Hinzufügen</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
