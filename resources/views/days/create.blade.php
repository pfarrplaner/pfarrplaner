@extends('layouts.app')

@section('content')
    <style>
        .uper {
            margin-top: 40px;
        }
    </style>
    <div class="card uper">
        <div class="card-header">
            Tag hinzufügen
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div><br />
            @endif
            <form method="post" action="{{ route('days.store') }}">
                <div class="form-group">
                    @csrf
                    <label for="date">Datum</label>
                    <input type="text" class="form-control" name="date" placeholder="dd.mm.YYYY" value="__.{{str_pad($month, 2, 0, STR_PAD_LEFT)}}.{{$year}}"/>
                </div>
                <div class="form-group">
                    <label for="name">Bezeichnung des Tages</label>
                    <input type="text" class="form-control" name="name" placeholder="leer lassen für automatischen Eintrag"/>
                </div>
                <div class="form-group">
                    <label for="description">Hinweise zum Tag</label>
                    <textarea class="form-control" name="description"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </form>
        </div>
    </div>
@endsection
