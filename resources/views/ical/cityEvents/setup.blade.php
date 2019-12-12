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
                    @selectize(['name' => 'city', 'label' => 'Veranstaltungen für diese Kirchengemeinde anzeigen', 'items' => $cities])
                    <hr />
                    <input type="submit" class="btn btn-primary" value="Weiter &gt;" />
                </form>
            </div>
        </div>
    @endcomponent
@endsection