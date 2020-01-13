@extends('layouts.app')

@section('title', 'Anmelden')

@section('content')
    @component('components.ui.card')
        @slot('cardHeader')Pfarrplaner DEMO @endslot
        <h1>Herzlich willkommen zur Demoversion des Pfarrplaners</h1>
        <p>Dieses System dient zur Demonstration der aktuellen Features des Pfarrplaners. Es wird jede Nacht mit anonymisierten Daten aus der realen
            kirchlichen Arbeitswelt aktualisiert. </p>
    @endcomponent
    @component('components.ui.card')
        @slot('cardHeader')Demo: Benutzerkonten @endslot
        <table class="table table-hover table-striped">
        @foreach($users as $user)
            @if ($user->password != '')
            <tr>
                <td>{{ $user->fullName(true) }}<br />
                    @foreach($user->homeCities as $city)
                        <div class="badge badge-secondary">{{ $city->name }}</div>
                    @endforeach
                </td>
                <td>
                    @foreach($user->roles as $role)
                        <div class="badge badge-info">{{ $role->name }}</div>
                    @endforeach
                </td>
                <td>{{ $user->email }}</td>
                <td>
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <input type="hidden" name="email" value="{{ $user->email }}" />
                        <input type="password" name="password" @if($user->name=='Admin')value="admin" @else value="test" @endif/>
                        <input type="submit" class="btn btn-secondary" value="Anmelden" />
                    </form>
                </td>
            </tr>
            @endif
        @endforeach
        </table>
    @endcomponent
@endsection

@section('scripts')
        <script>
            $(document).ready(function(){
                $('#email').focus();
            });
        </script>
@endsection