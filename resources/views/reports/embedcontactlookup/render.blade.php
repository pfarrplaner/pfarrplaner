@extends('layouts.app')

@section('title', 'HTML-Code einbinden')

@section('content')
    @component('reports.embedhtml')<script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script defer>$(document).ready(function () {
                var url = '{{ $url }}';
                var parish;
                if (parish=localStorage.getItem('parish')) url = url + '&parish='+parish;
                fetch(url).then((res) => {return res.text();}).then((data) => {$('#{{ $randomId }}').html(data);});});
        </script>
        <div id="{{ $randomId }}">Bitte warten, Daten werden geladen...</div>
    @endcomponent
@endsection

@section('scripts')
    <script src="{{ asset('js/pfarrplaner/copy-code.js') }}"></script>
@endsection
