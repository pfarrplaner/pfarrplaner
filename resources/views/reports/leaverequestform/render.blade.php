<!DOCTYPE html>
<html lang="de">
<head>
    <style>
        @page {
            margin: 1.5cm 2.7cm 1.5cm 2.7cm;
            odd-footer-name: html_PageFooter;
            even-footer-name: html_PageFooter;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
        }

        input {
            border: 0;
            border-color: white;
            background-color: white;
            font-size: 1em;
            font-family: "Courier New", Courier, monospace;
        }

        .input-wrapper {
            border: 0;
            padding-bottom: 2px;
            padding-right: 2px;
            border-bottom: solid 1px black;
        }

        .input-wrapper input {
            width: 100%;
        }

        h1 {
            font-size: 12pt;
            font-weight: bold;
            margin-bottom: 1em;
        }

        h2 {
            margin-bottom: 1em;
            font-weight: bold;
            font-size: 10.5pt;
        }
        h3 {
            margin-bottom: 0;
            font-weight: bold;
            font-size: 10.5pt;
            text-decoration: underline;
        }

        .footer {
            width: 100%;
            text-align: right;
            font-size: .5em;
            color: darkgray;
        }

    </style>
</head>
<body>
<form>
    <table border="0" cellpadding="0" cellspacing="0" style="margin-bottom: 1em;">
        <tr>
            <td style="padding-right: 1cm">
                <div class="input-wrapper"  style="width: 62mm;">
                    <input type="text" name="username" value="{{ $absence->user->last_name }}, {{ $absence->user->first_name }}"  style="width: 62mm;"/>
                </div>
            </td>
            <td>
                <div class="input-wrapper"  style="width: 40mm;">
                    <input type="text" name="city" value="{{ $absence->user->cities->first()->name }}"  style="width: 40mm;"/>
                </div>
            </td>
            <td>,</td>
            <td>
                <div class="input-wrapper"  style="width: 27mm;">
                    <input type="text" name="date" value="{{ \Carbon\Carbon::now()->setTimezone('Europe/Berlin')->format('d.m.Y') }}"  style="width: 27mm;"/>
                </div>
            </td>
        </tr>
        <tr>
            <td style="padding-right: 1cm; text-align: center">(Name)</td>
            <td style="text-align: center">(Ort)</td>
            <td></td>
            <td style="text-align: center">(Datum)</td>
        </tr>
    </table>
    <div style="margin-bottom: 2em;">
        <table border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td style="width: 48mm;" valign="top">
                    Evang. Dekanatamt<br />
                    Charlottenstr. 16<br />
                    72336 Balingen
                </td>
                <td valign="bottom">
                    <span style="font-weight: bold; font-style: italic">Fax: 07433/7286</span>
                </td>
            </tr>
        </table>
    </div>
    <h1>Urlaubsantrag</h1>
    <div>
        Für die Zeit vom
        <span class="input-wrapper" style="width: 42mm;">
            <input type="text" name="from" value="{{ $absence->from->format('d.m.Y') }}" style="width: 42mm;" />
        </span>
        bis
        <span class="input-wrapper" style="width: 38mm;">
            <input type="text" name="to" value="{{ $absence->to->format('d.m.Y') }}" style="width: 38mm;" />
        </span>
        beantrage ich
    </div>
    <table border="0" cellpadding="0" cellspacing="0" style="margin-top: 0.3em; margin-bottom: 1em;">
        <tr  style="padding-top: 0.2em;">
            <td style="width: 13mm;" valign="top">&nbsp;</td>
            <td style="width: 63mm;" valign="top">Erholungsurlaub</td>
            <td valign="center" style="padding-bottom: 2px;">
                insgesamt
                <span class="input-wrapper" style="width: 14mm;">
                    <input type="text" name="cat1" value="{{ $duration }}" style="width: 14mm; text-align: right;" />
                </span>
                Kalendertage
            </td>
        </tr>
        <tr  style="height: 1.3em; padding-top: 0.2em;">
            <td style="width: 13mm;" valign="top">&nbsp;</td>
            <td style="width: 63mm;" valign="top">Tagungsurlaub</td>
            <td valign="center" style="padding-bottom: 2px;">
                insgesamt
                <span class="input-wrapper" style="width: 14mm;">
                    <input type="text" name="cat2" value="" style="width: 14mm;" />
                </span>
                Kalendertage
            </td>
        </tr>
        <tr  style="height: 1.3em; padding-top: 0.2em;">
            <td style="width: 13mm;" valign="top">&nbsp;</td>
            <td style="width: 63mm;" valign="top">Dienstbefreiung</td>
            <td valign="center" style="padding-bottom: 2px;">
                insgesamt
                <span class="input-wrapper" style="width: 14mm;">
                    <input type="text" name="cat3" value="" style="width: 14mm;" />
                </span>
                Kalendertage
            </td>
        </tr>
        <tr  style="height: 1.3em; padding-top: 0.2em;">
            <td style="width: 13mm;" valign="top">&nbsp;</td>
            <td style="width: 63mm;" valign="top">Dienstl. Abwesenheit</td>
            <td valign="center" style="padding-bottom: 2px;">
                insgesamt
                <span class="input-wrapper" style="width: 14mm;">
                    <input type="text" name="cat4" value="" style="width: 14mm;" />
                </span>
                Kalendertage
            </td>
        </tr>
        <tr  style="height: 1.3em; padding-top: 0.2em;">
            <td style="width: 13mm;" valign="top">&nbsp;</td>
            <td style="width: 63mm;" valign="top">Dienstfreie Tage</td>
            <td valign="center" style="padding-bottom: 2px;">
                insgesamt
                <span class="input-wrapper" style="width: 14mm;">
                    <input type="text" name="cat5" value="" style="width: 14mm;" />
                </span>
                Kalendertage
            </td>
        </tr>
        <tr  style="height: 1.3em; padding-top: 0.2em;">
            <td style="width: 13mm;" valign="top">&nbsp;</td>
            <td style="width: 63mm;" valign="top">für folgende Sonn- und Feiertage:</td>
            <td valign="center" style="padding-bottom: 2px;">
                <span class="input-wrapper" style="width: 54mm;">
                    <input type="text" name="cat6" value="" style="width: 54mm;" />
                </span>
            </td>
        </tr>
    </table>
    <h2>
        (s. Auszug aus der Urlaubs- und Stellvertretungsordnung auf der Rückseite)
    </h2>
    <table border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 1em; width: 145mm;">
        <tr>
            <td>Urlaubs-/Tagungsanschrift:</td>
            <td valign="center" style="padding-bottom: 2px;">
                <div class="input-wrapper" style="width: 102mm;">
                    <input type="text" style="width: 102mm;" name="remote_address1">
                </div>
            </td>
        </tr>
        <tr><td colspan="2"></td></tr>
        <tr>
            <td></td>
            <td valign="center" style="padding-bottom: 2px;">
                <div class="input-wrapper" style="width: 102mm;">
                    <input type="text" style="width: 102mm;" name="remote_address2">
                </div>
            </td>
        </tr>
    </table>
    <h2>
        Die Stellvertretung ist wie folgt geregelt:
    </h2>
    <table border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 0.1em; width: 145mm;">
        <tr>
            <td style="width: 38mm;">Im Pfarramt:</td>
            <td style="width: 106mm; padding-bottom: 2px;">
                <div class="input-wrapper" style="width: 106mm; height: 3em;">
                    <input type="text" style="width: 106mm;" name="pfarramt" value="{{ $absence->replacementText() }}"/>
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 38mm;">Gottesdienstvertretung:</td>
            <td style="width: 106mm; padding-bottom: 2px;">
                <div class="input-wrapper" style="width: 106mm; height: 3em;">
                    <input type="text" style="width: 106mm;" name="gd" />
                </div>
            </td>
        </tr>
        <tr>
            <td style="width: 38mm;">Im Religionsunterricht:</td>
            <td style="width: 106mm; padding-bottom: 2px;">
                <div class="input-wrapper" style="width: 106mm; height: 3em;">
                    <input type="text" style="width: 106mm;" name="gd" />
                </div>
            </td>
        </tr>
    </table>
    <div>
        <input type="checkbox" name="kein_ru" value="1" /> Religionsunterricht ist nicht betroffen.
    </div>
    <h2>
        Für den Religionsunterricht hat der Antragsteller / die Antragstellerin die Regelung der Stellvertretung
        rechtzeitig mit der Schulleitung abzusprechen.
    </h2>
    <table border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 1em; width: 145mm;">
        <tr>
            <td style="width: 38mm;">Bemerkungen:</td>
            <td style="width: 106mm; padding-bottom: 2px;">
                <div class="input-wrapper" style="width: 106mm;">
                    <input type="text" style="width: 106mm;" name="bemerkungen" />
                </div>
            </td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td style="width: 38mm;">Unterschrift:</td>
            <td style="width: 106mm; padding-bottom: 2px;">
                <div class="input-wrapper" style="width: 106mm;">
                    <input type="text" style="width: 106mm;" name="unterschrift" />
                </div>
            </td>
        </tr>
    </table>
    <div class="margin-bottom: 1em;">
        Die Urlaubsvertreter/innen sind verständigt und mit der Vertretung einverstanden.
    </div>
    <hr />
    <h3 style="margin-top: 1em;">Verfügung des Dekanatamts:</h3>
    <div>1. Mit dem beantragten Urlaub und der Vertretungsregelung einverstanden.</div>
    <table border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 1em; width: 145mm;">
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td style="width: 38mm; padding-left: 1cm;">Schuldekanin:</td>
            <td style="width: 106mm; padding-bottom: 2px;">
                <div class="input-wrapper" style="width: 106mm">
                    <input type="text" style="width: 106mm;" name="schuldekanin" />
                </div>
            </td>
        </tr>
        <tr><td colspan="3">&nbsp;</td></tr>
        <tr>
            <td style="width: 38mm; padding-left: 1cm;">Dekan:</td>
            <td style="width: 106mm; padding-bottom: 2px;">
                <div class="input-wrapper" style="width: 106mm">
                    <input type="text" style="width: 106mm;" name="dekan" />
                </div>
            </td>
        </tr>
    </table>
    <table border="0" cellspacing="0" cellpadding="0" style="margin-bottom: 1em; width: 145mm;">
        <tr>
            <td style="width: 4mm">2.</td>
            <td colspan="3">a) Urlaub genehmigt (per Fax/Kopie)</td>
        </tr>
        @for($year = $absence->from->year-1; $year <= $absence->to->year; $year ++)
        <tr>
            <td></td>
            <td style="width: 44mm;">@if($year == $absence->from->year-1) b) Urlaubsliste ergänzt:@endif</td>
            <td style="width: 43mm;">Restanspruch {{ $year }}: </td>
            <td style="width: 43mm; padding-bottom: 2px;">
                <span class="input-wrapper" style="width: 14mm;">
                    <input type="text" name="rest{{ $year }}" value="" style="width: 14mm; text-align: right" />
                </span>
                Kalendertage
            </td>
        </tr>
        @endfor
    </table>
</form>
<htmlpagefooter name="PageFooter">
    <div class="footer">
        Automatisch erstellt mit Pfarrplaner v{{ $version }} für {{ Auth::user()->name }} ({{ \Carbon\Carbon::now()->setTimezone('Europe/Berlin')->format('d.m.Y H:i:s') }})
    </div>
</htmlpagefooter>

</body>
</html>
