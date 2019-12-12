@extends('layouts.app')

@section('title', 'Kennzeichnung anlegen')

@section('content')
    <form method="post" action="{{ route('tags.store') }}">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary">Hinzufügen</button>
            @endslot
            @input(['name' => 'name', 'label' => 'Name:'])
            @input(['name' => 'code', 'label' => 'Code:'])
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
