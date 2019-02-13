@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="card mt-5">
            <div class="card-header">
                Tag bearbeiten
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif
                <form id="frmEdit" method="post" action="{{ route('days.update', $day->id) }}">
                    @method('PATCH')
                    @csrf
                    <div class="form-group">
                        <label for="date">Datum</label>
                        <input type="text" class="form-control" name="date" placeholder="dd.mm.YYYY"
                               value="{{ $day->date->format('d.m.Y') }}" @if (!Auth::user()->isAdmin) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="name">Bezeichnung des Tages</label>
                        <input type="text" class="form-control" name="name" value="{{ $day->name }}"  placeholder="leer lassen für automatischen Eintrag" @if (!Auth::user()->isAdmin) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="description">Hinweise zum Tag</label>
                        <textarea class="form-control" name="description">{{ $day->description }}</textarea>
                    </div>
                    <button id="btnSave" type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            $('#btnSave').click(function(event){
                event.preventDefault();
                $('input[name=date]').attr('disabled', false);
                $('input[name=name]').attr('disabled', false);
                $('#frmEdit').submit();
            });
        });
    </script>
@endsection
