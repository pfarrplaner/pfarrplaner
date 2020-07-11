@extends('layouts.app')

@section('title', 'Herzlich willkommen')

@section('content')
    @component('components.ui.card')
        <h1>Willkommen, {{ $user->name }}!</h1>
        <a class="btn btn-primary btn-lg" href="{{ route('calendar') }}"><span class="fa fa-calendar"></span> Zum Kalender</a>
        <a class="btn btn-secondary btn-lg" href="{{ route('baptisms.create') }}"><span class="fa fa-water"></span> Taufe anlegen...</a>
        <a class="btn btn-secondary btn-lg" href="{{ route('funerals.wizard') }}"><span class="fa fa-cross"></span> Beerdigung anlegen...</a>
        <a class="btn btn-secondary btn-lg" href="{{ route('weddings.wizard') }}"><span class="fa fa-ring"></span> Trauung anlegen...</a>
        <hr />

        <div class="row">
            <div class="col-md-3">
                <div class="card-counter primary" id="ctrServices" data-route="{{ route('calendar') }}" data-update-route="{{ route('counter', 'services') }}">
                    <i class="fa fa-calendar"></i>
                    <span class="count-numbers">0</span>
                    <span class="count-name">Gottesdienste</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter primary" id="ctrCities" data-route="{{ route('cities.index') }}"  data-update-route="{{ route('counter', 'cities') }}">
                    <i class="fa fa-map-marker"></i>
                    <span class="count-numbers">0</span>
                    <span class="count-name">Kirchengemeinden</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter primary" id="ctrUsers" data-route="{{ route('users.index') }}"  data-update-route="{{ route('counter', 'users') }}">
                    <i class="fa fa-user"></i>
                    <span class="count-numbers">0</span>
                    <span class="count-name">Benutzer</span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card-counter primary" id="ctrOnline" data-route="{{ route('locations.index') }}" data-update-route="{{ route('counter', 'online') }}">
                    <i class="fa fa-users"></i>
                    <span class="count-numbers">0</span>
                    <span class="count-name">Online</span>
                </div>
            </div>
        </div>
        <hr />
        <div><b>Online: </b><span id="onlineUsers"></span></div>
        <hr />
        <h2>Logs</h2>
        @foreach ($logs as $entry)
            <div class="alert alert-light }} log-entry-{{$entry['level']}}">
                <b style="font-size: 0.7em;"><span class="badge badge-{{ $entry['level_class'] }}"><span class="fa fa-{{ $entry['level_img'] }}"></span>&nbsp;{{ strtoupper($entry['level']) }}</span>
                    {{ (new \Carbon\Carbon($entry['date']))->format('Y-m-d H:i:s') }}</b> <br />
                {!! $entry['text'] !!}
                @if($entry['stack'])
                    <hr />
                    <a class="btn btn-sm btn-{{ $entry['level_class'] }} btnToggleStack">Stack Trace</a><br />
                <pre style="display:none;">{!! $entry['stack'] !!}</pre>
                @endif
            </div>
        @endforeach
    @endcomponent
@endsection

@section('scripts')
    <script>

        function ucfirst(s) {
            return s[0].toUpperCase() + s.slice(1);
        }

        function updateSingleCounter(tag) {
            var id = '#ctr'+ucfirst(tag);
            fetch($(id).data('update-route')).then(
                response => response.json()
            ).then(function(response) {
                $(id+' .count-numbers').html(response.count);
            });
        }

        function updateCounters() {
            updateSingleCounter('users');
            updateSingleCounter('services');
            updateSingleCounter('cities');

            fetch($('#ctrOnline').data('update-route')).then(
                response => response.json()
            ).then(function(response) {
                $('#ctrOnline .count-numbers').html(response.count);
                $('#onlineUsers').html('');
                var t = [];
                $.each(response.data.users, function(user) {
                    t.push(this.name+' ('+this.email+')');
                    $('#onlineUsers').html($('#onlineUsers').html()+' <span class="badge badge-secondary" title="'+this.email+'">'+this.name+'</span>');
                    $('#ctrOnline').attr('title', t.join('; '));
                })
            });


        }

        function enableStackTraceButtons() {
            $('.btnToggleStack').click(function(e){
                e.preventDefault();
                $(this).parent().find('pre').first().toggle();
            });
        }


        $(document).ready(function(){
            setTimeout(updateCounters, 1000);
            updateCounters();
            enableStackTraceButtons();

            $('.card-counter').click(function(e) {
                if ($(this).data('route')) {
                    window.location.href=$(this).data('route');
                }
            });
        });
    </script>

@endsection
