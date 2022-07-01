@extends('errors::minimal')

@section('title', __('Interner Serverfehler'))
@section('code', '500')
@section('message')
    <div class="text-bold">Interner Serverfehler</div>
    <div>Ein Fehlerbericht wurde bereits automatisch versandt.</div>
@endsection
@section('button')
    <div style="margin-top: 1em;">
        <a style="padding: .75em; border: solid 1px black; border-radius: .25em;"
           href="mailto:christoph.fischer@elkw.de">Eigene Anmerkungen senden</a>
    </div>
@endsection
