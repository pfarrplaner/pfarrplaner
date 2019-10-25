@extends('layouts.app')
@section('title', 'Benutzer')


@section('content')
    @component('components.container')
        <ul class="nav nav-tabs" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" href="#users" role="tab" data-toggle="tab">Benutzer</a>
            </li>
            @can('benutzer-bearbeiten')
            <li class="nav-item">
                <a class="nav-link" href="#otherPeople" role="tab" data-toggle="tab">Weitere Personen</a>
            </li>
            @endcan
        </ul>

        <div class="tab-content" style="padding-top: 15px;">
            <div role="tabpanel" class="tab-pane fade in active show" id="users">
                <div class="form-group">
                    <input class="form-control search-field" type="text" placeholder="Suchen nach ..." data-target="tblUsers"/>
                </div>
                <table class="table table-striped" id="tblUsers">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Kirchengemeinde(n)</th>
                        <th>E-Mailadresse</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($users as $user)
                        <tr>
                            <td>{{$user->name}}@if($user->title) ({{ $user->title }}) @endif <br />
                                @foreach($user->homeCities as $city)
                                    <span class="badge badge-secondary">{{ $city->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @foreach($user->visibleCities as $city)
                                    <span class="badge @if($user->writableCities->contains($city)) badge-success @else badge-warning @endif">{{ $city->name }}</span>
                                @endforeach
                            </td>
                            <td>{{$user->email}}<br />
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info" title="@foreach($role->permissions()->get() as $p) {{ $p->name }} @endforeach">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @can('update', $user)
                                    <a href="{{ route('users.edit',$user->id)}}" class="btn btn-sm btn-primary" title="Bearbeiten">
                                        <span class="fa fa-edit"></span>
                                    </a>
                                    <a href="{{ route('user.services', $user->id) }}" class="btn btn-sm btn-secondary" title="Gottesdienste anzeigen">
                                        <span class="fa fa-search"></span>
                                    </a>
                                @endcan
                                @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user->id)}}" method="post" class="form-inline" style="display:inline;">
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
            </div>
            @can('benutzer-bearbeiten')
            <div role="tabpanel" class="tab-pane fade" id="otherPeople">
                <p>Hier findest du eine Übersicht von Personen, die irgendwo im Plan eingetragen wurden, aber keinen eigenen Benutzerzugang haben. Du kannst eine Person zum Benutzer machen, indem du ihr Benutzernamen und Passwort gibst.</p>
                <div class="form-group">
                    <input class="form-control search-field" type="text" placeholder="Suchen nach ..." data-target="tblPeople"/>
                </div>
                <table class="table table-striped" id="tblPeople">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Kirchengemeinde(n)</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($otherPeople as $user)
                        <tr>
                            <td>{{$user->name}}@if($user->title) ({{ $user->title }}) @endif <br />
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @foreach($user->homeCities as $city)
                                    <span class="badge badge-secondary">{{ $city->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @can('update', $user)
                                    <a href="{{ route('users.edit',$user->id)}}" class="btn btn-sm btn-primary" title="Bearbeiten">
                                        <span class="fa fa-edit"></span>
                                    </a>
                                @endcan
                                @can('delete', $user)
                                    <form action="{{ route('users.destroy', $user->id)}}" method="post" class="form-inline" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" type="submit" title="Löschen"><span class="fa fa-trash"></span></button>
                                    </form>
                                @endcan
                                @can('update', $user)
                                    <a href="{{ route('user.services', $user->id) }}" class="btn btn-sm btn-secondary" title="Gottesdienste anzeigen">
                                        <span class="fa fa-search"></span>
                                    </a>
                                        <form action="{{ route('user.join', $user->id)}}" method="post" class="form-inline" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-secondary" type="submit" title="Mit einer anderen Person zusammenführen"><span class="fa fa-object-group"></span></button>
                                        </form>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            @endcan
        </div>

        <hr/>
        @can('create', \App\User::class)
        <a class="btn btn-secondary" href="{{ route('users.create') }}">Neuen Benutzer hinzufügen</a>
        @endcan
        </div>
        <script>
            $(document).ready(function(){
                $('.search-field').on('keyup', function(){
                    var value = $(this).val().toLowerCase();
                    if (value != '') {
                        $('table#'+$(this).data('target')+' tbody tr').each(function() {
                            if ($(this).find('td').first().html().toLowerCase().includes(value)) {
                                $(this).show();
                            } else {
                                $(this).hide();
                            }
                        });
                    } else {
                        $('table#'+$(this).data('target')+' tbody tr').show();
                    }
                });
                $('.search-field').first().focus();
            });
        </script>
    @endcomponent
@endsection
