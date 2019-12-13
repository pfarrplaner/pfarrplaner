@extends('layouts.app')

@section('title', 'Kirchen und Gottesdienstorte')

@section('navbar-left')
    @can('kirche-bearbeiten')
        <li class="nav-item">
            <a class="btn btn-success" href="{{ route('locations.create') }}">Neuen Ort hinzufügen</a>
        </li>
    @endcan
@endsection

@section('content')
    @component('components.ui.card')
        <table class="table table-striped table-hover">
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
                    <td class="text-right" style="min-width: 100px;">
                        @if(Auth::user()->isAdmin || (Auth::user()->can('kirche-bearbeiten') && Auth::user()->cities->contains($location->city)))
                            <a href="{{ route('locations.edit',$location->id)}}" class="btn btn-primary  btn-sm" title="Bearbeiten"><span class="fa fa-edit"></span></a>
                            <form action="{{ route('locations.destroy', $location->id)}}" method="post" style="display: inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger btn-sm" type="submit" title="Löschen"><span class="fa fa-trash"></span></button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    @endcomponent
@endsection
