@extends('layouts.app')

@section('content')
    @component('components.container')
        <h1>Neue Funktionen</h1>
        <ul>
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
