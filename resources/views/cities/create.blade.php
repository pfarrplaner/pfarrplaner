@extends('layouts.app')

@section('title', 'Kirchengemeinde hinzufügen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Kirchengemeinde hinzufügen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('cities.store') }}">
                    @csrf
                    @tabheaders
                        @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => true]) @endtabheader
                        @tabheader(['id' => 'offerings', 'title' => 'Opfer']) @endtabheader
                    @endtabheaders
                    @tabs
                        @tab(['id' => 'home', 'active' => true])
                            @input(['name' => 'name', 'label' => 'Ort']) @endinput
                            @input(['name' => 'public_events_calendar_url', 'label' => 'URL für einen öffentlichen Kalender auf elkw.de']) @endinput
                        @endtab
                        @tab(['id' => 'offerings'])
                            @input(['name' => 'default_offering_goal', 'label' => 'Opferzweck, wenn nicht angegeben']) @endinput
                            @input(['name' => 'default_offering_description', 'label' => 'Opferbeschreibung bei leerem Opferzweck']) @endinput
                            @input(['name' => 'default_funeral_offering_goal', 'label' => 'Opferzweck für Beerdigungen']) @endinput
                            @input(['name' => 'default_funeral_offering_description', 'label' => 'Opferbeschreibung für Beerdigungen']) @endinput
                            @input(['name' => 'default_wedding_offering_goal', 'label' => 'Opferzweck für Trauungen']) @endinput
                            @input(['name' => 'default_wedding_offering_description', 'label' => 'Opferbeschreibung für Trauungen']) @endinput
                        @endtab
                    </div>
                    <hr/>
                    <button type="submit" class="btn btn-primary">Hinzufügen</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
