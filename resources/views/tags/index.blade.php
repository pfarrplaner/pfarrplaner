@extends('layouts.app')

@section('title', 'Tags')

@section('content')
    @component('components.container')
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Tag</th>
                <th>Code</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($tags as $tag)
                <tr>
                    <td>{{$tag->name}}</td>
                    <td>{{$tag->code}}</td>
                    <td>
                        @can('tags-bearbeiten')
                            <a href="{{ route('tags.edit',$tag->id)}}" class="btn btn-primary" title="Bearbeiten"><span class="fa fa-edit"></span></a>
                            <form action="{{ route('tags.destroy', $tag->id)}}" method="post">
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
        @can('tags-bearbeiten')
            <hr/>
            <a class="btn btn-secondary" href="{{ route('tags.create') }}">Neues Tag hinzufügen</a>
        @endcan
    @endcomponent
@endsection
