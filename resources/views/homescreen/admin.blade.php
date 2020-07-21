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
@foreach($levels->all() as $level)
    <span class="badge badge-{{ $levels->cssClass($level) }} level on" data-level="{{ $level }}" data-levelclass="{{ $levels->cssClass($level) }}" style="cursor: pointer;">{{ $level }}</span>
@endforeach
        <pre>
@foreach ($logs as $entry)
<span class="logline logline-{{ $entry['level'] }}"><span class="@if($entry['stack'])btnToggleStack @endif badge badge-{{ $entry['level_class'] }}" title="{{ strtoupper($entry['level']) }}"><span class="fa fa-{{ $entry['level_img'] }}"></span> {{ $entry['level'] }}</span> {{ (new \Carbon\Carbon($entry['date']))->setTimezone('Europe/Berlin')->format('Y-m-d H:i:s') }} {!! $entry['text'] !!} @if($entry['stack'])<div class="stacktrace" style="display:none; background-color: black; border: solid 1px darkgray; color: lightgreen; font-size: .7em;"><br />{!! $entry['stack'] !!}</div> @endif<br /></span>@endforeach
        </pre>
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
                $(this).parent().find('.stacktrace').first().toggle();
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

            $('.level').click(function(){
                if($(this).hasClass('on')) {
                    $(this).removeClass('on').addClass('off');
                    $(this).removeClass('badge-' + $(this).data('levelclass')).addClass('badge-secondary');
                    $('.logline-'+$(this).data('level')).hide();
                } else {
                    $(this).addClass('on').removeClass('off');
                    $(this).addClass('badge-' + $(this).data('levelclass')).removeClass('badge-secondary');
                    $('.logline-'+$(this).data('level')).show();
                }
            });
        });
    </script>

@endsection
