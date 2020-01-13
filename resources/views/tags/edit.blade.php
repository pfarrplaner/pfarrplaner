@extends('layouts.app')

@section('title', 'Kennzeichnung bearbeiten')

@section('content')
    <form method="post" action="{{ route('tags.update', $tag) }}">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Speichern</button>
            @endslot
            @input(['name' => 'name', 'label' => 'Name:', 'value' => $tag->name])
            @input(['name' => 'code', 'label' => 'Code:', 'value' => $tag->code])
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function ($) {
            $('#name').focus();
        });
    </script>
@endsection
