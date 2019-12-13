@extends('layouts.app')
@section('title', 'Kirchengemeinden')


@section('navbar-left')
    @can('create', \App\City::class)
        <li class="nav-item">
            <a class="btn btn-success" href="{{ route('cities.create') }}">Neue Kirchengemeinde hinzufügen</a>
        </li>
    @endcan
@endsection

@section('content')
    @component('components.ui.card')
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Kirchengemeinde</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($cities as $city)
                <tr>
                    <td>{{$city->name}}</td>
                    <td class="text-right">
                        @can('update', $city)
                            <a href="{{ route('cities.edit',$city->id)}}" class="btn btn-sm btn-primary"
                               title="Bearbeiten">
                                <span class="fa fa-edit"></span>
                            </a>
                        @endcan
                        @can('delete', $city)
                            <form action="{{ route('cities.destroy', $city->id)}}" method="post" class="form-inline"
                                  style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" type="submit" title="Löschen"><span
                                            class="fa fa-trash"></span></button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endcomponent
@endsection
