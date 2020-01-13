@extends('layouts.app')
@section('title', 'Änderungsprotokoll')


@section('content')
    @component('components.container')
        <form method="post" action="{{ route('revisions.index.post') }}">
            @csrf
            <div class="row">
                <div class="col-3 form-group">
                    @selectize(['name' => 'key', 'label' => 'Schlüssel', 'items' => $keys])
                </div>
                <div class="col-3 form-group">
                    <label for="old">Alt</label>
                    <input class="form-control" type="text" name="old" value="{{ $old }}">
                </div>
                <div class="col-3 form-group">
                    <label for="new">Neu</label>
                    <input class="form-control" type="text" name="new" value="{{ $new }}">
                </div>
            </div>
            <div class="row">
                <div class="col-12 form-group">
                    <input class="btn btn-primary" type="submit" value="Filtern" />
                </div>
            </div>
        </form>
        <form method="post" action="{{ route('revisions.revert') }}">
            @csrf
            @hidden(['name' => 'key', 'value' => $key])
            <table class="table table-striped" id="tblUsers">
                <thead>
                <tr>
                    <th><input id="toggler" type="checkbox" /></th>
                    <th>Gottesdienst</th>
                    <th>Benutzer</th>
                    <th>Datum</th>
                    <th>Feld</th>
                    <th>Alt</th>
                    <th>Neu</th>
                </tr>
                </thead>
                <tbody>
                @foreach ($revisions as $revision)
                    <tr>
                        <td><input type="checkbox" name="revisions[]" value="{{ $revision->id }}" /></td>
                        <td>{{ $revision->revisionable_id }}</td>
                        <td>{{ $revision->userResponsible()->name }}</td>
                        <td>{{ $revision->userResponsible()->created_at }}</td>
                        <td>{{ $revision->key }}</td>
                        <td>{{ $revision->oldValue() }}</td>
                        <td>{{ $revision->newValue() }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="form-group">
                <input class="btn btn-primary" type="submit" value="Ausgewählte rückgängig machen" />
            </div>
        </form>
    @endcomponent
    <script>
        $(document).ready(function() {
            $("#toggler").click(function() {
                $("input[name=revisions\\[\\]]").prop('checked', $(this).prop('checked'));
            });
        });
    </script>
@endsection
