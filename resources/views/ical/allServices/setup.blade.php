@extends('layouts.app')
@section('title', 'Mit Outlook verbinden')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Mit Outlook verbinden
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('ical.link', ['key' => $calendarLink->getKey()]) }}">
                    @csrf
                    <div class="form-group"> <!-- Radio group !-->
                        <label class="control-label">Diese Kirchengemeinden mit einbeziehen:</label>
                        @foreach ($cities as $city)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="includeCities[]" value="{{ $city->id }}"
                                       id="defaultCheck{{$city->id}}" checked>
                                <label class="form-check-label" for="defaultCheck{{$city->id}}">
                                    {{$city->name}}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <hr />
                    <input type="submit" class="btn btn-primary" value="Weiter &gt;" />
                </form>
            </div>
        </div>
    @endcomponent
@endsection