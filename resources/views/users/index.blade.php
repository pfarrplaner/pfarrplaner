@extends('layouts.app')
@section('title', 'Benutzer')

@section('navbar-left')
    @can('create', \App\User::class)
        <li class="nav-item">
            <a class="btn btn-success" href="{{ route('user.create') }}"><i class="fa fa-user-plus"></i> Neuen Benutzer
                hinzufügen</a>
        </li>
    @endcan
@endsection

@section('content')
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" href="#users" role="tab" data-toggle="tab">Benutzer</a>
        </li>
        @if(Auth::user()->can('benutzer-bearbeiten') || Auth::user()->isLocalAdmin)
            <li class="nav-item">
                <a class="nav-link" href="#otherPeople" role="tab" data-toggle="tab">Weitere Personen</a>
            </li>

        @endif
    </ul>

    <div class="tab-content" style="padding-top: 15px;">
        <div role="tabpanel" class="tab-pane fade in active show" id="users">
            @component('components.ui.card')
                @slot('cardHeader')Suchen @endslot
                <div class="form-group">
                    <input class="form-control search-field" type="text" placeholder="Suchen nach ..."
                           data-target="tblUsers"/>
                </div>
            @endcomponent
            @component('components.ui.card')
                <table class="table table-striped table-hover" id="tblUsers">
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
                            <td>{{$user->name}}@if($user->title) ({{ $user->title }}) @endif <br/>
                                @foreach($user->homeCities as $city)
                                    <span class="badge badge-secondary">{{ $city->name }}</span>
                                @endforeach
                            </td>
                            <td>
                                @foreach($user->visibleCities as $city)
                                    <span class="badge @if($user->writableCities->contains($city)) badge-success @else badge-warning @endif badge-permission-{{ $city->pivot->permission }}">{{ $city->name }}</span>
                                @endforeach
                            </td>
                            <td>{{$user->email}}<br/>
                                @foreach($user->roles as $role)
                                    <span class="badge badge-info"
                                          title="@foreach($role->permissions()->get() as $p) {{ $p->name }} @endforeach">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="text-right">
                                @can('update', $user)
                                    <a href="{{ route('user.edit',$user->id)}}" class="btn btn-sm btn-primary"
                                       title="Bearbeiten">
                                        <span class="fa fa-edit"></span>
                                    </a>
                                    <a href="{{ route('user.services', $user->id) }}"
                                       class="btn btn-sm btn-secondary" title="Gottesdienste anzeigen">
                                        <span class="fa fa-search"></span>
                                    </a>
                                @endcan
                                @if(\Illuminate\Support\Facades\Auth::user()->isAdmin && (Auth::user()->id != $user->id))
                                    <a href="{{ route('user.switch', $user) }}" class="btn btn-sm btn-secondary" title="Als {{ $user->name }} anmelden">
                                        <span class="fa fa-sign-in-alt"></span>
                                    </a>
                                @endif
                                @can('delete', $user)
                                    <form action="{{ route('user.destroy', $user->id)}}" method="post"
                                          class="form-inline" style="display:inline;">
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
        </div>
        @if(Auth::user()->can('benutzer-bearbeiten') || Auth::user()->isLocalAdmin)
            <div role="tabpanel" class="tab-pane fade" id="otherPeople">
                <div class="alert alert-info">Hier findest du eine Übersicht von Personen, die irgendwo im Plan
                    eingetragen wurden, aber keinen
                    eigenen Benutzerzugang haben. Du kannst eine Person zum Benutzer machen, indem du ihr
                    Benutzernamen und Passwort gibst.
                </div>
                @component('components.ui.card')
                    @slot('cardHeader')Suchen @endslot
                    <div class="form-group">
                        <input class="form-control search-field" type="text" placeholder="Suchen nach ..."
                               data-target="tblPeople"/>
                    </div>
                @endcomponent
                @component('components.ui.card')
                    <table class="table table-striped table-hover" id="tblPeople">
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
                                <td>{{$user->name}}@if($user->title) ({{ $user->title }}) @endif <br/>
                                    @foreach($user->roles as $role)
                                        <span class="badge badge-info">{{ $role->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach($user->homeCities as $city)
                                        <span class="badge badge-secondary">{{ $city->name }}</span>
                                    @endforeach
                                </td>
                                <td class="text-right">
                                    @can('update', $user)
                                        <a href="{{ route('user.edit',$user->id)}}" class="btn btn-sm btn-primary"
                                           title="Bearbeiten">
                                            <span class="fa fa-edit"></span>
                                        </a>
                                    @endcan
                                    @can('delete', $user)
                                        <form action="{{ route('user.destroy', $user->id)}}" method="post"
                                              class="form-inline" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" type="submit" title="Löschen"><span
                                                        class="fa fa-trash"></span></button>
                                        </form>
                                    @endcan
                                    @can('update', $user)
                                        <a href="{{ route('user.services', $user->id) }}"
                                           class="btn btn-sm btn-secondary" title="Gottesdienste anzeigen">
                                            <span class="fa fa-search"></span>
                                        </a>
                                        <form action="{{ route('user.join', $user->id)}}" method="post"
                                              class="form-inline" style="display:inline;">
                                            @csrf
                                            <button class="btn btn-sm btn-secondary" type="submit"
                                                    title="Mit einer anderen Person zusammenführen"><span
                                                        class="fa fa-object-group"></span></button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                @endcomponent
            </div>
        @endif
    </div>

    <hr/>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.search-field').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                if (value != '') {
                    $('table#' + $(this).data('target') + ' tbody tr').each(function () {
                        if ($(this).find('td').first().html().toLowerCase().includes(value)) {
                            $(this).show();
                        } else {
                            $(this).hide();
                        }
                    });
                } else {
                    $('table#' + $(this).data('target') + ' tbody tr').show();
                }
            });
            $('.search-field').first().focus();
        });
    </script>
@endsection
