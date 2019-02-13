@extends('layouts.app')

@section('content')
    @component('components.container')
        <h1>Mit Outlook verbinden:</h1>

        <p>Sie können eine Übersicht aller ihrer Gottesdienste oder auch aller Gottesdienste ausgewählter Gemeinden als Kalender in ihr Microsoft Outlook mit einbinden.
            Dazu brauchen Sie zunächst einen der folgenden Links (markieren und mit Strg+C kopieren). Bitte beachten Sie, dass diese Links mit jeder Änderung ihres
            Passworts neu erzeugt werden. Der bisherige Link ist dann ungültig.
        </p>

        <b>Für einen Kalender, der nur Ihre eigenen Gottesdienste enthält:</b>
        <pre>{{ route('ical.private', ['name' => $name, 'token' => urlencode($token)]) }}</pre>

        <b>Für einen Kalender, der alle Gottesdienste folgender Kirchengemeinden enthält:</b>
        <div class="form-group"> <!-- Radio group !-->
            <label class="control-label">Diese Kirchengemeinden mit einbeziehen:</label>
            @foreach ($cities as $city)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="includeCities[]" value="{{ $city->id }}"
                           id="defaultCheck{{$city->id}}" checked>
                    <label class="form-check-label" for="defaultCheck{{$city->id}}">
                        {{$city->name}}
                    </label>
                </div>
            @endforeach
        </div>
        <pre id="pre2">{{ env('APP_URL').'ical/gemeinden//'.urlencode($token) }}</pre>

        <style>
        </style>
        <h3>So gehen Sie zum Einbinden in Microsoft Outlook vor</h3>
        <ol class="steps">
            <li>Öffnen Sie Microsoft Outlook und klicken Sie links oben auf "Datei".</li>
            <li>Klicken Sie auf "Kontoeinstellungen" &gt; "Konten hinzufügen oder entfernen bzw. vorhandene Verbindungseinstellungen ändern."<br />
                <img class="img img-fluid" src="{{asset('img/steps/step1.jpg')}}" /></li>
            <li>Wechseln Sie in den Reiter "Internetkalender" und klicken Sie auf "Neu..."<br />
                <img class="img img-fluid" src="{{asset('img/steps/step2.jpg')}}" /></li>
            <li>Drücken Sie Strg+V, um den kopierten Link einzufügen und klicken Sie dann auf "Hinzufügen"<br />
                <img class="img img-fluid" src="{{asset('img/steps/step3.jpg')}}" /></li>
            <li>Geben Sie dem Kalender einen Namen und klicken Sie auf "OK".<br />
                <img class="img img-fluid" src="{{asset('img/steps/step4.jpg')}}" /></li>
        </ol>
        <p>Der Kalender erscheint nun wie gewohnt in der Kalenderansicht.</p>

        <script>
            var appUrl = '{{ env('APP_URL') }}';
            var ids = [];

            function updateUrl() {
                ids = [];
                $('input[type=checkbox]:checked').each(function(){
                   ids.push($(this).val());
                });
                $('#pre2').html('{{ env('APP_URL') }}ical/gemeinden/'+ids.join(',')+'/{{ $token }}');
            }

            $(document).ready(function(){
                updateUrl();
                $('input[type=checkbox]').change(function(){
                    updateUrl();
                });
            });
        </script>


    @endcomponent
@endsection
