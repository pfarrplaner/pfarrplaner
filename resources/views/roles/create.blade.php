@extends('layouts.app')

@section('title', 'Benutzerrolle hinzufügen')

@section('content')
    <form method="post" action="{{ route('roles.store') }}">
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Speichern</button>
            @endslot
            @csrf
            @input(['label' =>  'Name', 'name' => 'name'])
            @selectize(['label' => 'Berechtigungen', 'name'=> 'permissions[]', 'items' => $permissions])
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