@extends('layouts.app')

@section('title', 'Benutzerrolle bearbeiten')

@section('content')
    <form method="post" action="{{ route('roles.update', $role->id) }}">
        @method('PATCH')
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Speichern</button>
            @endslot
            @input(['label' =>  'Name', 'name' => 'name', 'value' => $role->name]) @endinput
            @selectize(['label' => 'Berechtigungen', 'name'=> 'permissions[]', 'items' => $permissions, 'value' => $role->permissions]) @endselectize
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {

            $('.permissionSelect').selectize({
                create: true,
                render: {
                    option_create: function (data, escape) {
                        return '<div class="create">Neue Berechtigung anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                    }
                },
            });

        });
    </script>
@endsection
