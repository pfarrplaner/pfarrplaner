@extends('layouts.app')
@section('title', 'Mit Outlook verbinden')

@section('content')
    @component('components.ui.card')
        <ol class="steps">
            <li>Öffne Microsoft Outlook und wechsle zur Kalenderansicht.</li>
            <li>Wähle "Start &gt; Kalender öffnen &gt; Aus dem Internet..."</li>
            <li>Gib als Speicherort folgenden Link ein:<br>
                <div class="input-group mb-3">
                    <input id="link" type="text" class="form-control" placeholder="Link zum Kalenderexport"
                           aria-label="Link zum Kalenderexport" aria-describedby="basic-addon2"
                           value="{{ $calendarLink->getLink() }}">
                    <div class="input-group-append">
                        <button class="btn btn-outline-secondary" type="button" title="In die Zwischenablage kopieren">
                            <span class="fa fa-clipboard"></span></button>
                    </div>
                </div>
            </li>
            <li>Klicke auf "Ok".</li>
        </ol>
        <p>Der Kalender erscheint nun wie gewohnt in der Kalenderansicht.</p>
    @endcomponent
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.btn-outline-secondary').click(function () {
                $('#link').select();
                document.execCommand('copy');
            });

            $('#link').focus();
            $('#link').select();
        });
    </script>
@endsection