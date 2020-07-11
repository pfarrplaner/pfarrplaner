<?php
/**
 * Pfarrplaner
 *
 * @package Pfarrplaner
 * @author Christoph Fischer <chris@toph.de>
 * @copyright (c) 2020 Christoph Fischer, https://christoph-fischer.org
 * @license https://www.gnu.org/licenses/gpl-3.0.txt GPL 3.0 or later
 * @link https://github.com/potofcoffee/pfarrplaner
 * @version git: $Id$
 *
 * Sponsored by: Evangelischer Kirchenbezirk Balingen, https://www.kirchenbezirk-balingen.de
 *
 * Pfarrplaner is based on the Laravel framework (https://laravel.com).
 * This file may contain code created by Laravel's scaffolding functions.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Created by PhpStorm.
 * User: Christoph Fischer
 * Date: 17.05.2019
 * Time: 08:46
 */

namespace App\Misc;


use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

/**
 * Class VersionInfo
 * @package App\Misc
 */
class VersionInfo
{

    /**
     * @return mixed
     */
    public static function version() {
        $data = json_decode(file_get_contents(base_path('composer.json')), true);
        return $data['version'];
    }

    /**
     * Get version "what's new" messages
     * @return Collection
     */
    public static function getMessages()
    {
        return collect([
            [
                'date' => new Carbon('2020-06-11 09:30', 'Europe/Berlin'),
                'text' => 'Der Pfarrplaner bietet nun eine Integration für die <a href="https://konfiapp.de" target="_blank">KonfiApp</a>. Wenn diese aktiviert ist, können automatisch QR-Codes für Gottesdienste angelegt werden. Die Konfis können diese dann mit der KonfiApp scannen und Punkte sammeln.',
            ],
            [
                'date' => new Carbon('2020-02-27 10:00', 'Europe/Berlin'),
                'text' => 'Aus Gottesdiensten, die einen Link zu einer Audioaufnahme haben, kann nun automatisch ein Podcast erstellt werden. Dieser findet sich unter https://www.pfarrplaner.de/podcasts/<name des ortes>.xml',
            ],
            [
                'date' => new Carbon('2020-02-25 11:00', 'Europe/Berlin'),
                'text' => 'Gottesdienste können nun Angaben zu Streaming, usw. enthalten. Diese können auf der Homepage eingeblendet werden.',
            ],
            [
                'date' => new Carbon('2020-02-27 11:00', 'Europe/Berlin'),
                'text' => 'Urlaubseinträge können nun Hinweise für die Vertretung (z.B. mögliche Beerdigungstermine) enthalten.',
            ],
            [
                'date' => new Carbon('2020-02-17 11:00', 'Europe/Berlin'),
                'text' => 'Benutzer mit Startbildschirm können in ihrem <a href="/user/profile">Profil</a> einstellen, dass sie eine Liste aller Gottesdienste sehen wollen, bei denen bestimmte Felder nicht ausgefüllt sind. ',
            ],
            [
                'date' => new Carbon('2020-01-23 11:30', 'Europe/Berlin'),
                'text' => 'Mit dem Sammeleingabe <a href="/input/planning">"Planungstabelle"</a> können beliebige Dienstzuweisungen für alle Gottesdienste eines Jahres bearbeitet werden.',
            ],
            [
                'date' => new Carbon('2020-01-23 08:50', 'Europe/Berlin'),
                'text' => 'Gottesdienste können nun einen Titel (z.B. "Friedensgebet") haben.',
            ],
            [
                'date' => new Carbon('2020-01-11 16:32', 'Europe/Berlin'),
                'text' => 'Gottesdienste können nun beliebig viele Dateianhänge haben.',
            ],
            [
                'date' => new Carbon('2020-01-11 16:27', 'Europe/Berlin'),
                'text' => 'Kasualien können nun beliebig viele Dateianhänge haben.',
            ],
            [
                'date' => new Carbon('2020-01-10 17:27', 'Europe/Berlin'),
                'text' => 'In den Einstellungen der Kalenderansicht kann nun ein Filter für die angezeigten Gottesdienstorte eingerichtet werden.',
            ],
            [
                'date' => new Carbon('2020-01-10 16:48', 'Europe/Berlin'),
                'text' => 'In den Einstellungen der Kalenderansicht kann nun das anzuzeigende Namensformat gewählt werden.',
            ],
            [
                'date' => new Carbon('2019-12-07 16:00', 'Europe/Berlin'),
                'text' => 'Der Pfarrplaner erscheint seit heute im neuen, übersichtlicheren Layout.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '25.11.2019'),
                'text' => 'Beim Export nach Outlook (rechts oben auf <a href="'.route('ical.connect').'"><span class="fa fa-calendar-alt"></span></a> klicken) kann nun ein kombinierter Veranstaltungskalender für eine Gemeinde erzeugt werden. Dieser kombiniert Einträge aus dem Pfarrplaner, aus einem freigegebenen Outlookkalender und aus dem Online-Planer des Gemeindebaukastens.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '23.11.2019'),
                'text' => 'Es gibt jetzt ein <a href="'.route('reports.list').'"> Ausgabeformat</a> namens "Terminliste", mit dem eine Worddatei mit Gottesdiensten und Terminen für einen bestimmten Zeitraum erzeugt werden kann.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '23.11.2019'),
                'text' => 'Für die Kinderkirche kann nun auch eine abweichende Uhrzeit angegeben werden.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '14.11.2019'),
                'text' => 'Gottesdiensten können nun neben den bereits bestehenden Diensten weitere, beliebig benannte Dienste zugeordnet werden (z.B. Schriftlesung)',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '14.11.2019'),
                'text' => 'Für jede <a href="'.route('cities.index').'">Kirchengemeinde</a> können nun Standard-Opferzwecke für Beerdigungen und Trauungen angegeben werden. Diese werden im Opferplan separat aufgelistet und bei entsprechenden Gottesdiensten mit leerem Opferzweck-Feld automatisch eingesetzt.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '11.11.2019'),
                'text' => 'Es gibt jetzt ein <a href="'.route('reports.list').'"> Ausgabeformat</a> namens "Opferplan", mit dem der komplette Opferplan für ein Jahr ausgedruckt werden kann.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '07.11.2019'),
                'text' => 'Jeder Gottesdienst hat nun ein Feld "Opferbetrag", in dem die Höhe des eingesammelten Opfers vermerkt werden kann (z.B. für Bekanntmachungen). Dieses Feld kann auch über die Sammeleingabe Opferplan bearbeitet werden.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '02.11.2019'),
                'text' => 'Jeder Gottesdienst hat nun ein Feld "Interne Anmerkungen" für Beschreibungen, die nicht veröffentlicht werden sollen, aber intern im Pfarrplaner angezeigt werden.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '02.11.2019'),
                'text' => 'Es gibt jetzt ein <a href="'.route('reports.list').'"> Ausgabeformat</a> namens "Kirchzettel", das automatisch den wöchtentlichen Ausgang für den Schaukasten zusammenstellt. Dabei können Veranstaltungen aus einem Gemeindekalender auf Sharepoint übernommen werden. (Das muss allerdings erst eingerichtet werden. Bei Fragen dazu bitte an den Christoph Fischer wenden).',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '02.11.2019'),
                'text' => 'Es gibt jetzt ein <a href="'.route('reports.list').'"> Ausgabeformat</a> namens "Bekanntgaben", das automatisch die Bekanntgaben für einen Gottesdienst zusammenstellt. Dabei können Veranstaltungen aus einem Gemeindekalender auf Sharepoint übernommen werden. (Das muss allerdings erst eingerichtet werden. Bei Fragen dazu bitte an den Christoph Fischer wenden).',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '29.10.2019'),
                'text' => 'Es gibt jetzt ein <a href="'.route('reports.list').'"> Ausgabeformat</a> namens "Liste der Angehörigen", das alle Angehörigen der vergangenen Beerdigungen auflistet, um diese zum Ewigkeitssonntag einzuladen.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '29.10.2019'),
                'text' => 'Bessere Möglichkeiten zum Export nach Outlook (rechts oben auf <a href="'.route('ical.connect').'"><span class="fa fa-calendar-alt"></span></a> klicken).',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '25.10.2019'),
                'text' => 'Der Pfarrplaner kann jetzt zur <a href="'.route('absences.index').'">Urlaubsplanung</a> für freigeschaltete Benutzer genutzt werden.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '17.10.2019'),
                'text' => 'Unter "Admin > <a href="'.route('calendar', ['year' => null, 'month' => null, 'slave' => 1]).'">Automatische Kalenderansicht öffnen</a> kann ein neues Kalenderfenster geöffnet werden, das automatisch allen Änderungen folgt (z.B. zur Anzeige auf dem Beamer).',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '17.10.2019'),
                'text' => 'Der Pfarrplaner ist auf die Domain www.pfarrplaner.de umgezogen. Alle bisherigen Links funktionieren aber weiterhin',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '27.09.2019'),
                'text' => 'Unter "Sammeleingabe > <a href="'.route('inputs.setup', ['input' => 'multipleServices']).'">Mehrere Gottesdienste anlegen</a> angelegte Gottesdienste können nun auf andere Wochentage außer Sonntag gelegt werden. Außerdem wird eine Sammelbenachrichtigung per E-Mail versendet.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '27.09.2019'),
                'text' => 'Unter "Sammeleingabe > <a href="'.route('inputs.setup', ['input' => 'multipleServices']).'">Mehrere Gottesdienste anlegen</a> können nun viele Gottesdienste auf einmal angelegt werden (z.B. für das neue Jahr).',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '13.09.2019'),
                'text' => 'Es ist jetzt möglich, Benutzer so anzulegen, dass sie nur bestimmte Gemeinden sehen. Wenn du die nötigen Rechte hast, kannst du im Menü unter <span class="fa fa-wrench"></span> Admin > <a href="'.route('users.index').'">Benutzer</a> sehen, wer welchen Zugriff auf deine Kirchengemeinde(n) hat.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '07.09.2019'),
                'text' => 'Im <a href="'.route('user.profile').'">Benutzerprofil</a> (<span class="fa fa-user"></span> '.Auth::user()->fullName(false).' > Mein Profil) kann jetzt eingestellt werden, welche Kirchengemeinden im Kalender in welcher Reihenfolge angezeigt werden sollen. Außerdem kann dort eine neue, (experimentelle) vertikale Tabellendarstellung ausgewählt werden, die die Tage in Zeilen und die Kirchengemeinden in Spalten anordnet.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '06.09.2019'),
                'text' => 'Bei Taufen kann zum Taufgespräch nun auch eine Uhrzeit angegeben werden. Das Taufgespräch kann dann mit einem Klick in den Outlookkalender übernommen werden.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '06.09.2019'),
                'text' => 'Bei Bestattungen können jetzt auch Kontaktdaten (Telefonnummer usw.) für die Angehörigen mit gespeichert werden.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '06.08.2019'),
                'text' => 'Eine automatisch aktualisierte Übersicht der nächsten Gottesdienste kann in die Homepage der Kirchengemeinde (Gemeindebaukasten) '
                .'eingebunden werden. Mehr dazu aus der Kalenderansicht unter "Ausgaben..." > "Liste von Gottesdiensten"',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '05.08.2019'),
                'text' => 'Einzelne Gottesdienste können nun per Klick in Outlook übernommen werden.'
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '18.07.2019'),
                'text' => 'Pfarrer können Trauungen direkt vom Startbildschirm aus anlegen.'
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y H:i:s', '17.05.2019 18:00:00'),
                'text' => 'E-Mailbenachrichtungen für neue/geänderte Gottesdienste können nun selbst im Menü unter  <a href="'.route('user.profile').'">'
                .'<span class="fa fa-user"></span>&nbsp;'
                .Auth::user()->name.' > Mein Profil</a> an- und abbestellt werden.',
            ],
            [
                'date' => Carbon::createFromFormat('d.m.Y', '14.05.2019'),
                'text' => 'E-Mailbenachrichtigungen zu neuen/geänderten Gottesdienste enthalten nun eine .ics-Datei im Anhang, die als
                Termin direkt in Outlook importiert werden kann.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '13.05.2019'),
                'text' => 'Kommentare können nun auch bei Kasualien angelegt werden.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '13.05.2019'),
                'text' => 'Beerdigungen können nun von Pfarrern vom Startbildschirm aus angelegt werden. Dabei werden die Einträge für
                Tag (falls noch nicht angelegt) und Gottesdienst automatisch angelegt.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '13.05.2019'),
                'text' => 'Das Schließen der Detailansicht eines Gottesdienstes löst nur noch dann einen Speichervorgang (und eine
                E-Mailbenachrichtigung) aus, wenn tatsächlich etwas verändert wurde.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '13.05.2019'),
                'text' => 'Taufen können nun zunächst als Taufantrag ohne Datum angelegt werden (über den Button, der bei Pfarrern und
                beim Kirchenregisteramt auf der Startseite erscheint).'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '24.04.2019'),
                'text' => 'Für jeden Benutzer gibt es jetzt einen Startbildschirm mit einer Übersicht der für ihn relevanten Daten.
        Für unterschiedliche Benutzertypen gibt es dabei verschiedene Varianten dieses Startbildschirms.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '24.04.2019'),
                'text' => 'Gottesdiensten können nun Kasualien (Bestattung, Taufe, Trauung) mit den entsprechenden Daten zugeordnet
                werden. Alle persönlichen Daten werden dabei ausschließlich verschlüsselt gespeichert und erst für die Anzeige
                entschlüsselt.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '03.04.2019'),
                'text' => 'Beim Anlegen von Tagen, die nur für bestimmte Gemeinden angezeigt werden sollen ("lilane Tage") ist es nun
                möglich, auch "fremde" Gemeinden auszuwählen.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '29.03.2019'),
                'text' => '"Lilane" Tage, die einen Gottesdienst enthalten, für den der aktuelle Nutzer eingeteilt sind,
                werden snun automatisch aufgeklappt.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '08.03.2019'),
                'text' => 'Der Organistenplan kann nun unter "Sammeleingabe > Organistenplan" für ein ganzes Jahr auf einmal
                bearbeitet werden.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '08.03.2019'),
                'text' => 'Es können nun Tage angelegt werden, die nur für bestimmte Gemeinden angezeigt werden sollen. Diese Tage
                werden im Kalender als lila Streifen angezeigt und erst auf ein Klicken hin eingeblendet.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '28.02.2019'),
                'text' => 'Der Urlaub der Pfarrer wird nur noch den Personen angezeigt, die auch Pfarrer einteilen dürfen.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '28.02.2019'),
                'text' => 'Nur Personen, die allgemeine Gottesdienstdaten bearbeiten dürfen, können auch neue Gottesdienste anlegen.'
            ],


            [
                'date' => Carbon::createFromFormat('d.m.Y', '28.02.2019'),
                'text' => 'Der Plan für die Kinderkirche kann nun ohne Login unter '.url('kinderkirche/{kirchengemeinde}').' (also z.B.
                <a href="'.url('kinderkirche/tailfingen').'">'.url('kinderkirche/tailfingen').'</a>) eingesehen werden.
        Außerdem steht der Plan unter "Ausgabe > Programm für die Kinderkirche" als PDF-Datei zur Verfügung.'
                            ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '28.02.2019'),
                    'text' => 'Der Plan für die Kinderkirche kann nun für ein ganzes Jahr unter "Sammeleingabe > Kinderkirche" bearbeitet werden.'
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '28.02.2019'),
                    'text' => 'Separate Benutzerrechte für die Bearbeitung von Informationen zu Opfer und Kinderkirche.'
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '28.02.2019'),
                    'text' => 'Neue Felder für weitere am Gottesdienst Beteiligte und Kinderkirche.'
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '22.02.2019'),
                    'text' => 'Das Gottesdienstformular ist übersichtlicher in mehrere Reiter unterteilt.'
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '22.02.2019'),
                    'text' => 'Der komplette Opferplan einer Gemeinde für ein Jahr kann jetzt unter "Sammeleingabe > Opferplan" auf einmal bearbeitet werden.'
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '22.02.2019'),
                    'text' => 'Die verschiedenen Ausgabeformate sind jetzt übersichtlicher auf einer separaten Seite angeordnet. Bei der
                Ausgabe für den Gemeindebrief kann zwischen verschiedenen Formaten (Tailfingen, Truchtelfingen) gewählt werden.'
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '20.02.2019'),
                    'text' => 'Öffentlich (d.h. ohne Passwort) einsehbarer Vertretungsplan unter ' . url('vertretungen')
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '14.02.2019'),
                    'text' => 'Mit "Berichte &gt; Jahresplan der Gottesdienste" kann eine Exceldatei mit einem Jahresplan aller Gottesdienste (inkl. Opfer) erzeugt werden.'
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '14.02.2019'),
                    'text' => 'Zu den Gottesdiensten können nun auch Opferzweck, Opferzähler, usw. angegeben werden. "Taufe" und "Abendmahl" sind Ankreuzfelder geworden, die den
                entsprechenden Text automatisch in die Anmerkungen einfügen und z.B. in Statistiken, usw. relevant sind.'
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '13.02.2019'),
                    'text' => 'Mit "Berichte &gt; Prädikantenanforderung" kann das Prädikantenformular für das Dekanat automatisch erstellt werden.'
                ],


                [
                    'date' => Carbon::createFromFormat('d.m.Y', '12.02.2019'),
                    'text' => 'Statt einen Pfarrer anzugeben, kann jetzt bei einem Gottesdienst angekreuzt werden, dass ein Prädikant benötigt wird. Dies wird im Kalender rot hervorgehoben.
        (Wenn der Eintrag "Prädikant benötigt" im Kalender nicht rot erscheint, Seite mit Strg+F5 neu laden.)'
                ],


        ]);
    }

}
