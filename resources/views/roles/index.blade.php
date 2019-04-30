@extends('layouts.app')

@section('title', 'Benutzerrollen')

@section('content')
    @component('components.container')
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Rolle</th>
                <th>Berechtigungen</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($roles->sortBy('name') as $role)
                <tr>
                    <td>{{$role->name}}</td>
                    <td>
                        @foreach($role->permissions->sortBy('name') as $permission)
                            <span class="badge badge-dark">{{ $permission->name }}</span>
                        @endforeach
                    </td>
                    <td>
                        @can('update', $role)
                            <a href="{{ route('roles.edit',$role->id)}}" class="btn btn-sm btn-primary" title="Bearbeiten">
                                <span class="fa fa-edit"></span>
                            </a>
                        @endcan
                        @can('delete', $role)
                            <form action="{{ route('roles.destroy', $role->id)}}" method="post" class="form-inline" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" title="Löschen"><span class="fa fa-trash"></span></button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <hr/>
        @can('create', \Spatie\Permission\Models\Role::class)
            <a class="btn btn-secondary" href="{{ route('roles.create') }}">Neue Rolle hinzufügen</a>
        @endcan
        </div>
    @endcomponent
@endsection
