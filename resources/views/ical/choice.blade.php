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
                    <p>Welche Art von Kalender möchtest du mit Outlook verbinden?</p>
                <div class="table-fluid">
                    <table class="table table-striped">
                        <thead></thead>
                        <tbody>
                        @foreach ($calendarLinks as $calendarLink)
                            <tr>
                                <td>
                                    <b>{{ $calendarLink->getTitle() }}</b><br/>
                                    {{ $calendarLink->getDescription() }}
                                </td>
                                <td>
                                    <a class="btn btn-primary" href="{{ $calendarLink->setupRoute() }}">
                                        Weiter &gt;
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endcomponent
@endsection