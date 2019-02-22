@extends('layouts.app')

@section('content')
    @component('components.container')
        <h1>Neue Funktionen</h1>
        <ul>
            <li>
                <b>22.02.2019:</b>
                Die verschiedenen Ausgabeformate sind jetzt übersichtlicher auf einer separaten Seite angeordnet. Bei der
                Ausgabe für den Gemeindebrief kann zwischen verschiedenen Formaten (Tailfingen, Truchtelfingen) gewählt werden.
            </li>
            <li>
                <b>20.02.2019:</b>
                Öffentlich (d.h. ohne Passwort) einsehbarer Vertretungsplan unter {{ env('APP_URL') }}vertretungen
            </li>
            <li>
                <b>14.02.2019:</b>
                Mit "Berichte &gt; Jahresplan der Gottesdienste" kann eine Exceldatei mit einem Jahresplan aller Gottesdienste (inkl. Opfer) erzeugt werden.
            </li>
            <li>
                <b>14.02.2019:</b>
                Zu den Gottesdiensten können nun auch Opferzweck, Opferzähler, usw. angegeben werden. "Taufe" und "Abendmahl" sind Ankreuzfelder geworden, die den
                entsprechenden Text automatisch in die Anmerkungen einfügen und z.B. in Statistiken, usw. relevant sind.
            </li>
            <li>
                <b>13.02.2019:</b>
                Mit "Berichte &gt; Prädikantenanforderung" kann das Prädikantenformular für das Dekanat automatisch erstellt werden.
            </li>
            <li>
                <b>12.02.2019:</b>
                        Statt einen Pfarrer anzugeben, kann jetzt bei einem Gottesdienst angekreuzt werden, dass ein Prädikant benötigt wird. Dies wird im Kalender rot hervorgehoben.
                (Wenn der Eintrag "Prädikant benötigt" im Kalender nicht rot erscheint, Seite mit Strg+F5 neu laden.)
            </li>
        </ul>
    @endcomponent
@endsection
