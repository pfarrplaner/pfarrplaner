@extends('layouts.app')

@section('title', 'Tag anlegen')

@section('content')
    @component('components.container')
    <div class="card">
        <div class="card-header">
            Tag hinzufügen
        </div>
        <div class="card-body">
            @component('components.errors')
            @endcomponent
            <form method="post" action="{{ route('tags.store') }}">
                <div class="form-group">
                    @csrf
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name"/>
                </div>
                <div class="form-group">
                    <label for="code">Code:</label>
                    <input type="text" class="form-control" name="code"/>
                </div>
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            </form>
        </div>
    </div>
    <script>
        $(document).ready(function($){
            $('#name').focus();
        });
    </script>
    @endcomponent
@endsection
