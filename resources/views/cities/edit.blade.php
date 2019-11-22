@extends('layouts.app')

@section('title', 'Kirchengemeinde bearbeiten')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Kirchengemeinde bearbeiten
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('cities.update', $city->id) }}" id="frm">
                    @method('PATCH')
                    @csrf
                    @tabheaders
                        @tabheader(['id' => 'home', 'title' => 'Allgemeines', 'active' => true]) @endtabheader
                        @tabheader(['id' => 'offerings', 'title' => 'Opfer']) @endtabheader
                        @tabheader(['id' => 'calendars', 'title' => 'Externe Kalender']) @endtabheader
                    @endtabheaders
                    @tabs
                        @tab(['id' => 'home', 'active' => true])
                            @input(['name' => 'name', 'label' => 'Ort', 'value' => $city->name, 'enabled' => Auth::user()->can('ort-bearbeiten')]) @endinput
                        @endtab
                        @tab(['id' => 'offerings'])
                            @input(['name' => 'default_offering_goal', 'label' => 'Opferzweck, wenn nicht angegeben', 'value' => $city->default_offering_goal]) @endinput
                            @input(['name' => 'default_offering_description', 'label' => 'Opferbeschreibung bei leerem Opferzweck', 'value' => $city->default_offering_description]) @endinput
                            @input(['name' => 'default_funeral_offering_goal', 'label' => 'Opferzweck für Beerdigungen', 'value' => $city->default_funeral_offering_goal]) @endinput
                            @input(['name' => 'default_funeral_offering_description', 'label' => 'Opferbeschreibung für Beerdigungen', 'value' => $city->default_funeral_offering_description]) @endinput
                            @input(['name' => 'default_wedding_offering_goal', 'label' => 'Opferzweck für Trauungen', 'value' => $city->default_wedding_offering_goal]) @endinput
                            @input(['name' => 'default_wedding_offering_description', 'label' => 'Opferbeschreibung für Trauungen', 'value' => $city->default_wedding_offering_description]) @endinput
                        @endtab
                        @tab(['id' => 'calendars'])
                            @input(['name' => 'public_events_calendar_url', 'label' => 'URL für einen öffentlichen Kalender auf elkw.de', 'value' => $city->public_events_calendar_url, 'enabled' => Auth::user()->can('ort-bearbeiten')]) @endinput
                            @input(['name' => 'op_domain', 'label' => 'Domain für den Online-Planer', 'value' => $city->op_domain, 'enabled' => Auth::user()->can('ort-bearbeiten')]) @endinput
                            @input(['name' => 'op_customer_key', 'label' => 'Kundenschlüssel (customer key) für den Online-Planer', 'value' => $city->op_customer_key, 'enabled' => Auth::user()->can('ort-bearbeiten')]) @endinput
                            @input(['name' => 'op_customer_token', 'label' => 'Token (customer token) für den Online-Planer', 'value' => $city->op_customer_token, 'enabled' => Auth::user()->can('ort-bearbeiten')]) @endinput
                        @endtab
                    @endtabs
                    <hr/>
                    <button id="btnSubmit" type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
        <script>
            $(document).ready(function(){
                $('#btnSubmit').click(function(e){
                    $('input').removeAttr('disabled');
                });
            });
        </script>
    @endcomponent
@endsection
