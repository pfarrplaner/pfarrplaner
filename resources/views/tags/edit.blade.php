@extends('layouts.app')

@section('title', 'Tag bearbeiten')

@section('content')
    @component('components.container')
    <div class="card">
        <div class="card-header">
            Tag bearbeiten
        </div>
        <div class="card-body">
            @component('components.errors')
            @endcomponent
            <form method="post" action="{{ route('tags.update', $tag) }}">
                <div class="form-group">
                    @csrf
                    @method('PATCH')
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" value="{{ $tag->name }}"/>
                </div>
                <div class="form-group">
                    <label for="code">Code:</label>
                    <input type="text" class="form-control" name="code" value="{{ $tag->code }}"/>
                </div>
                <button type="submit" class="btn btn-primary">Speichern</button>
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
