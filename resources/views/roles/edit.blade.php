@extends('layouts.app')

@section('title', 'Benutzerrolle bearbeiten')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Benutzerrolle bearbeiten
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br />
                @endif
                <form method="post" action="{{ route('roles.update', $role->id) }}">
                    @method('PATCH')
                    @csrf
                    <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" class="form-control" name="name" value="{{ $role->name }}" />
                    </div>
                    <hr />
                    <div class="form-group">
                        <label for="permissions[]">Berechtigungen</label>
                        <select class="form-control permissionSelect" name="permissions[]" multiple="multiple">
                            @foreach ($permissions as $permission)
                                <option value="{{ $permission->name }}" @if($role->permissions->contains($permission)) selected @endif>{{ $permission->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
        <script>



            $(document).ready(function(){

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
    @endcomponent
@endsection
