@extends('layouts.app')

@section('title', 'Pfarrämter')

@section('navbar-left')
    @can('parishes-bearbeiten')
        <li class="nav-item">
            <a class="btn btn-success" href="{{ route('parishes.create') }}">Neues Pfarramt hinzufügen</a>
        </li>
    @endcan
@endsection

@section('content')
    @component('components.ui.card')
        <table class="table table-striped table-hover">
            <thead>
            <tr>
                <th>Pfarramt</th>
                <th>Kirchengemeinde</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($parishes as $parish)
                <tr>
                    <td>{{$parish->name}}</td>
                    <td>{{$parish->owningCity->name}}</td>
                    <td class="text-right" style="min-width: 100px;">
                        @can('update', $parish)
                            <a href="{{ route('parishes.edit',$parish->id)}}" class="btn btn-primary"
                               title="Bearbeiten"><span class="fa fa-edit"></span></a>
                        @endcan
                        @can('delete', $parish)
                            <form action="{{ route('parishes.destroy', $parish->id)}}" method="post" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit" title="Löschen"><span
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
