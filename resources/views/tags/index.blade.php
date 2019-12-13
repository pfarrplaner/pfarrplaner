@extends('layouts.app')

@section('title', 'Kennzeichnungen')

@section('navbar-left')
    @can('tags-bearbeiten')
        <li class="nav-item">
            <a class="btn btn-success" href="{{ route('tags.create') }}">Neue Kennzeichnung hinzufügen</a>
        </li>
    @endcan
@endsection

@section('content')
    @component('components.ui.card')
        <table class="table table-striped table-hover">
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
                    <td class="text-right" style="min-width: 100px;">
                        @can('tags-bearbeiten')
                            <a href="{{ route('tags.edit',$tag->id)}}" class="btn btn-primary" title="Bearbeiten"><span class="fa fa-edit"></span></a>
                            <form action="{{ route('tags.destroy', $tag->id)}}" method="post" style="display:inline-block;">
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
    @endcomponent
@endsection
