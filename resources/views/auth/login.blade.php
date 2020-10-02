@extends('layouts.app')

@section('title', 'Anmelden')

@section('content')
    @component('components.container')
        <form method="POST" action="{{ route('login') }}">
            @csrf
            @component('components.ui.card')
                @slot('cardHeader')
                    Anmelden
                @endslot
                @slot('cardFooter')
                    <button type="submit" class="btn btn-primary">
                        Anmelden
                    </button>

                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            Passwort vergessen?
                        </a>
                    @endif
                @endslot
                    @input(['name' => 'email', 'label' => 'E-Mailadresse', 'value' => old('email'), 'type' => 'email', 'id' => 'email'])
                    @input(['name' => 'password', 'label' => 'Passwort', 'type' => 'password'])
                    @checkbox(['name' => 'remember', 'label' => 'Angemeldet bleiben', 'value' => old('remember')])
            @endcomponent
            @component('components.ui.card')
                @slot('collapseId')contactInfo @endslot
                @slot('cardHeader')
                    Anbieterinformationen
                @endslot
                <p><b>pfarrplaner.de</b> wird betrieben von:</p>
                <p>Pfarrer Christoph Fischer<br />für den Evangelischen Kirchenbezirk Balingen<br />Liegnitzer Str. 38<br />72461 Albstadt<br />
                    <br />
                    Fon 07432 3762<br />
                    Fax 07432 171760<br />
                    E-Mail <a href="mailto:christoph.fischer@elkw.de">christoph.fischer@elkw.de</a>
                </p>
            @endcomponent
    @endcomponent
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#email_input').focus();
        });
    </script>
@endsection
