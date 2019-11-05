@extends('layouts.app')

@section('title', 'Kirchen / Gottesdienstorte')

@section('content')
    @component('components.container')
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Kirche / Gottesdienstort</th>
                <th>Kirchengemeinde</th>
                <th>Gottesdienst um</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($locations as $location)
                <tr>
                    <td>{{$location->name}}</td>
                    <td>{{$location->city->name}}</td>
                    <td>{{\Carbon\Carbon::createFromTimeString($location->default_time)->format('H:i') }} Uhr</td>
                    <td>
                        @if(Auth::user()->isAdmin || (Auth::user()->can('kirche-bearbeiten') && Auth::user()->cities->contains($location->city)))
                            <a href="{{ route('locations.edit',$location->id)}}" class="btn btn-primary" title="Bearbeiten"><span class="fa fa-edit"></span></a>
                            <form action="{{ route('locations.destroy', $location->id)}}" method="post">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger" type="submit" title="Löschen"><span class="fa fa-trash"></span></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        @can('kirche-bearbeiten')
            <hr/>
            <a class="btn btn-secondary" href="{{ route('locations.create') }}">Neue Kirche / neuen Gottesdienstort hinzufügen</a>
        @endcan
    @endcomponent
@endsection
