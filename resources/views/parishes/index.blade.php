@extends('layouts.app')

@section('title', 'Pfarrämter')

@section('content')
    @component('components.container')
        <table class="table table-striped">
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
                    <td>
                        @can('parishes-bearbeiten')
                            <a href="{{ route('parishes.edit',$parish->id)}}" class="btn btn-primary" title="Bearbeiten"><span class="fa fa-edit"></span></a>
                            <form action="{{ route('parishes.destroy', $parish->id)}}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit" title="Löschen"><span class="fa fa-trash"></span></button>
                            </form>
                        @endcan
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @can('parishes-bearbeiten')
            <hr/>
            <a class="btn btn-secondary" href="{{ route('parishes.create') }}">Neues Pfarramt hinzufügen</a>
        @endcan
    @endcomponent
@endsection
