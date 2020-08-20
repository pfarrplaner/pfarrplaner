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
    @endcomponent
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#email_input').focus();
        });
    </script>
@endsection
