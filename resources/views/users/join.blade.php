@extends('layouts.app')

@section('title', 'Benutzer zusammenführen')


@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Benutzer zusammenführen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('user.join.finalize') }}">
                    @csrf
                    <input type="hidden" name="user1" value="{{ $user->id }}" />
                    <div class="form-group">
                        <label for="user2">Alle Einträge für Benutzer "{{ $user->fullName() }}" sollen auf folgenden Benutzer umgeschrieben werden:</label>
                        <select class="form-control fancy-selectize" name="user2">
                            @foreach($users as $thisUser)
                                <option value="{{ $thisUser->id }}">{{ $thisUser->fullName() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
