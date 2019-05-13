@extends('layouts.app')

@section('title', 'Neue Funktionen')

@section('content')
    @component('components.container')
        <h1>Neue Funktionen</h1>
        <ul>
            <li>
                <b>13.05.2019:</b>
                Das Schließen der Detailansicht eines Gottesdienstes löst nur noch dann einen Speichervorgang (und eine
                E-Mailbenachrichtigung) aus, wenn tatsächlich etwas verändert wurde.
            </li>
            <li>
                <b>13.05.2019:</b>
                Taufen können nun zunächst als Taufantrag ohne Datum angelegt werden (über den Button, der bei Pfarrern und
                beim Kirchenregisteramt auf der Startseite erscheint).
            </li>
            <li>
                <b>24.04.2019:</b>
                Für jeden Benutzer gibt es jetzt einen Startbildschirm mit einer Übersicht der für ihn relevanten Daten.
                Für unterschiedliche Benutzertypen gibt es dabei verschiedene Varianten dieses Startbildschirms.
            </li>
            <li>
                <b>24.04.2019:</b>
                Gottesdiensten können nun Kasualien (Bestattung, Taufe, Trauung) mit den entsprechenden Daten zugeordnet
                werden. Alle persönlichen Daten werden dabei ausschließlich verschlüsselt gespeichert und erst für die Anzeige
                entschlüsselt.
            </li>
            <li>
                <b>03.04.2019:</b>
                Beim Anlegen von Tagen, die nur für bestimmte Gemeinden angezeigt werden sollen ("lilane Tage") ist es nun
                möglich, auch "fremde" Gemeinden auszuwählen.
            </li>
            <li>
                <b>29.03.2019:</b>
                "Lilane" Tage, die einen Gottesdienst enthalten, für den der aktuelle Nutzer eingeteilt sind,
                werden snun automatisch aufgeklappt.
            </li>
            <li>
                <b>08.03.2019:</b>
                Der Organistenplan kann nun unter "Sammeleingabe > Organistenplan" für ein ganzes Jahr auf einmal
                bearbeitet werden.
            </li>
            <li>
                <b>08.03.2019:</b>
                Es können nun Tage angelegt werden, die nur für bestimmte Gemeinden angezeigt werden sollen. Diese Tage
                werden im Kalender als lila Streifen angezeigt und erst auf ein Klicken hin eingeblendet.
            </li>
            <li>
                <b>28.02.2019:</b>
                Der Urlaub der Pfarrer wird nur noch den Personen angezeigt, die auch Pfarrer einteilen dürfen.
            </li>
            <li>
                <b>28.02.2019:</b>
                Nur Personen, die allgemeine Gottesdienstdaten bearbeiten dürfen, können auch neue Gottesdienste anlegen.
            </li>
            <li>
                <b>28.02.2019:</b>
                Der Plan für die Kinderkirche kann nun ohne Login unter {{ env('APP_URL') }}kinderkirche/{kirchengemeinde} (also z.B.
                <a href="{{ env('APP_URL') }}kinderkirche/tailfingen">{{ env('APP_URL') }}kinderkirche/tailfingen</a>) eingesehen werden.
                Außerdem steht der Plan unter "Ausgabe > Programm für die Kinderkirche" als PDF-Datei zur Verfügung.
            </li>
            <li>
                <b>28.02.2019:</b>
                Der Plan für die Kinderkirche kann nun für ein ganzes Jahr unter "Sammeleingabe > Kinderkirche" bearbeitet werden.
            </li>
            <li>
                <b>28.02.2019:</b>
                Separate Benutzerrechte für die Bearbeitung von Informationen zu Opfer und Kinderkirche.
            </li>
            <li>
                <b>28.02.2019:</b>
                Neue Felder für weitere am Gottesdienst Beteiligte und Kinderkirche.
            </li>
            <li>
                <b>22.02.2019:</b>
                Das Gottesdienstformular ist übersichtlicher in mehrere Reiter unterteilt.
            </li>
            <li>
                <b>22.02.2019:</b>
                Der komplette Opferplan einer Gemeinde für ein Jahr kann jetzt unter "Sammeleingabe > Opferplan" auf einmal bearbeitet werden.
            </li>
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
