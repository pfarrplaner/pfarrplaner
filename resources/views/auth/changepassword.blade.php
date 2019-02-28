@extends('layouts.app')
@section('title', 'Passwort ändern')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">Passwort ändern</div>

            <div class="card-body">
                @if (session('error'))
                    <div class="alert alert-danger">
                        {{ session('error') }}
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <form class="form-horizontal" method="POST" action="{{ route('changePassword') }}">
                    {{ csrf_field() }}

                    <div class="form-group{{ $errors->has('current-password') ? ' has-error' : '' }}">
                        <label for="new-password" class="col-md-4 control-label">Aktuelles Passwort</label>

                        <div class="col-md-6">
                            <input id="current-password" type="password" class="form-control" name="current-password"
                                   required>

                            @if ($errors->has('current-password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('current-password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group{{ $errors->has('new-password') ? ' has-error' : '' }}">
                        <label for="new-password" class="col-md-4 control-label">Neues Passwort</label>

                        <div class="col-md-6">
                            <input id="new-password" type="password" class="form-control" name="new-password" required>

                            @if ($errors->has('new-password'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('new-password') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="new-password-confirm" class="col-md-4 control-label">Neues Passwort wiederholen</label>

                        <div class="col-md-6">
                            <input id="new-password-confirm" type="password" class="form-control"
                                   name="new-password_confirmation" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-6 col-md-offset-4">
                            <button type="submit" class="btn btn-primary">
                                Passwort ändern
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
