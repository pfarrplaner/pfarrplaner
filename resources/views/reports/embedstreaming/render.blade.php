@extends('layouts.app')

@section('title', 'HTML-Code einbinden')

@section('content')
    @component('reports.embedhtml')<script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script><script defer>$(document).ready(function () {fetch('{{ $url }}').then((res) => {return res.text();}).then((data) => {$('#{{ $randomId }}').html(data);});});</script><div id="{{ $randomId }}"><img src="https://www.pfarrplaner.de/img/spinner.gif" /> Bitte warten, Daten werden geladen...</div> Bitte warten, Daten werden geladen...</div>
    @endcomponent
@endsection

@section('scripts')
    <script src="{{ asset('js/pfarrplaner/copy-code.js') }}"></script>
@endsection
