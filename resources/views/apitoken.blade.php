@extends('layouts.app')

@section('title', 'API Token');

@section('content')
    @component('components.ui.card')
        <div class="input-group mb-3">
            <input id="token" type="text" class="form-control" placeholder="API token"
                   aria-label="API token" value="{{ $token }}">
            <div class="input-group-append">
                <button class="btn btn-outline-secondary" type="button" title="In die Zwischenablage kopieren">
                    <span class="fa fa-clipboard"></span></button>
            </div>
        </div>
    @endcomponent
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.btn-outline-secondary').click(function () {
                $('#token').select();
                document.execCommand('copy');
            });

            $('#link').focus();
            $('#link').select();
        });
    </script>
@endsection