@extends('layouts.app')

@section('content')
    @component('components.container')
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Name</th>
                <th>E-Mailadresse</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{$user->name}}</td>
                    <td>{{$user->email}}</td>
                    <td>
                        <a href="{{ route('users.edit',$user->id)}}" class="btn btn-sm btn-primary" title="Bearbeiten">
                            <span class="fa fa-edit"></span>
                        </a>
                        <form action="{{ route('users.destroy', $user->id)}}" method="post" class="form-inline" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" type="submit" title="Löschen"><span class="fa fa-trash"></span></button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <hr/>
        <a class="btn btn-secondary" href="{{ route('users.create') }}">Neuen Benutzer hinzufügen</a>
        </div>
    @endcomponent
@endsection
