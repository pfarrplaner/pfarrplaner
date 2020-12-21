@extends('layouts.app')

@section('title', 'Dienstanfrage senden')

@section('content')
    <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'send']) }}">
        @csrf
        @component('components.ui.card')
            @slot('cardHeader')
                Dienstanfrage erstellen (Schritt 3)
            @endslot
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Weiter &gt;</button>
            @endslot

            @textarea(['label' => 'Begleittext zur Anfrage', 'name' => 'text', 'placeholder' => 'Text, den du hier eingibst, erscheint in der E-Mail mit der Dienstanfrage'])

            <p>Bitte vervollständige die Liste der E-Mailadressen</p>

            @foreach ($users as $user)
                @input(['name' => 'address['.$user->id.']', 'value' => $user->email, 'label' => 'E-Mailadresse für '.$user->name, 'placeholder' => 'Leer lassen = Nachricht wird nicht gesendet'])
            @endforeach

            @hidden(['name' => 'locations', 'value' => $locations])
            @hidden(['name' => 'start', 'value' => $start])
            @hidden(['name' => 'end', 'value' => $end])
            @hidden(['name' => 'ministry', 'value' => $ministry])
            @hidden(['name' => 'services', 'value' => join(',', $services)])
            @hidden(['name' => 'recipients', 'value' => join(',', $recipients)])


        @endcomponent
    </form>
@endsection
