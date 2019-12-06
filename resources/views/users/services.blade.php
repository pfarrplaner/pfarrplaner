@extends('layouts.app')
@section('title', 'Gottesdienste für '.$user->fullName())


@section('content')
    @component('components.ui.card')
        <table class="table table-striped" id="tblUsers">
            <thead>
            <tr>
                <th colspan="5">Gottesdienst</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            @foreach($services as $service)
                <tr>
                    <td>{{ $service->city->name }}</td>
                    <td>{{ $service->day->date->format('d.m.Y') }}</td>
                    <td>{{ $service->timeText() }}</td>
                    <td>{{ $service->locationText() }}</td>
                    <td>{{ $service->descriptionText() }}</td>
                    <td class="text-right">
                        @can('update', $service)
                            <a href="{{ route('services.edit',$service->id)}}" class="btn btn-sm btn-primary"
                               title="Bearbeiten">
                                <span class="fa fa-edit"></span>
                            </a>
                        @endcan
                        @can('delete', $service)
                            <form action="{{ route('services.destroy', $service->id)}}" method="post" class="form-inline"
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
