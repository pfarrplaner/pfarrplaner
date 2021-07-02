<!DOCTYPE html>
<html lang="de">
<head>
    <style>
        @page {
            margin: 1cm 1cm 1cm 1.5cm;
            odd-footer-name: html_PageFooter;
            even-footer-name: html_PageFooter;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 7.5pt;
        }

        input, textarea {
            border: 0;
            border-color: white;
            background-color: white;
            font-size: 1em;
            font-family: "Courier New", Courier, monospace;
        }

        textarea {
            width: 180mm !important;
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

        .top-box {
            border: solid 1px black;
            background-color: #E0E0E0;
            width: 100%;
            padding: 0;
            margin: 0;
            text-align: center;
            font-size: 7.5pt;
        }

        h1 {
            margin: 0;
            padding: 0;
            font-size: 12pt;
            font-weight: bold;
            width: 100%;
            text-align: center;
            text-transform: uppercase;
        }

        h2 {
            margin-top: 6pt;
            margin-bottom: 3pt;
            font-weight: bold;
            font-size: 10pt;
            text-transform: uppercase;
        }

        h3 {
            margin-bottom: 0;
            font-weight: bold;
            font-size: 10.5pt;
            text-decoration: underline;
        }

        table.form {
            width: 100%;
            border-collapse: collapse;
        }

        table.form td {
            border: solid 1px darkgray;
        }

        table.layout {
            width: 100%;
        }

        table.debug {
            width: 100%;
            border-collapse: collapse;
        }

        table.layout, table.layout td, table.debug, table.debug td {
            border: 0;
            padding: 0;
            margin: 0;
        }

        table.debug td {
            border: solid 1px red;
        }

        .footer {
            width: 100%;
        }

        .footer td {
            font-size: 7pt;
            color: darkgray;
        }

    </style>
</head>
<body>
<form>
    <div class="top-box">
        <h1>Antrag auf Genehmigung einer Dienstreise</h1>
        <b>Diese Genehmigung ist der Reisekostenabrechnung beizufügen neben eventl. weiteren Belegen, da sonst keine
            Erstattung möglich ist.</b>
    </div>
    <h2> 1. Dienstreiseantrag</h2>
    <table class="form" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td width="50%">
                <table class="layout" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width: 2.5cm; padding: 2px;">Name</td>
                        <td style="padding: 2px;">
                            <input type="text" name="name" value="{{ $absence->user->last_name }}" style="width: 6cm;"/>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table class="layout" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width: 2.5cm; padding: 2px;">Vorname</td>
                        <td style="padding: 2px;">
                            <input type="text" name="first_name" value="{{ $absence->user->first_name }}"
                                   style="width: 6cm;"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="50%">
                <table class="layout" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width: 2.5cm; padding: 2px;">Dienststelle</td>
                        <td style="padding: 2px;">
                            <input type="text" name="office" value="{{ $absence->user->office }}" style="width: 6cm;"/>
                        </td>
                    </tr>
                </table>
            </td>
            <td width="50%">
                <table class="layout" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="width: 2.5cm; padding: 2px;">Telefon (dienstl.)</td>
                        <td style="padding: 2px;">
                            <input type="text" name="phone" value="{{ $absence->user->phone }}" style="width: 6cm;"/>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="layout">
                    <tr>
                        <td width="50%">
                            <table class="layout" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 2.5cm; padding: 2px;"><b>Hinfahrt</b> am</td>
                                    <td style="padding: 2px;">
                                        <input type="text" name="from_date"
                                               value="{{ $absence->from->format('d.m.Y') }}" style="width: 6cm;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 2.5cm; padding: 2px;">von</td>
                                    <td style="padding: 2px;">
                                        <input type="text" name="origin1" value="" style="width: 6cm;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 2.5cm; padding: 2px;">nach</td>
                                    <td style="padding: 2px;">
                                        <input type="text" name="destination1" value="" style="width: 6cm;"/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td width="50%">
                            <table class="layout" border="0" cellpadding="0" cellspacing="0">
                                <tr>
                                    <td style="width: 2.5cm; padding: 2px;"><b>Rückfahrt</b> am</td>
                                    <td style="padding: 2px;">
                                        <input type="text" name="to_date" value="{{ $absence->to->format('d.m.Y') }}"
                                               style="width: 6cm;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 2.5cm; padding: 2px;">von</td>
                                    <td style="padding: 2px;">
                                        <input type="text" name="origin2" value="" style="width: 6cm;"/>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="width: 2.5cm; padding: 2px;">nach</td>
                                    <td style="padding: 2px;">
                                        <input type="text" name="destination2" value="" style="width: 6cm;"/>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 2px;">
                <div style="margin-bottom: 6pt;">Grund der Dienstreise (bei Tagungen/Fort-, Aus- und
                    Weiterbildungsveranstaltungen bitte Programm o. ä. beifügen)
                </div>
                <textarea style="width: 98%; height: 1cm;" name="reason">{{ $absence->reason ?: '&nbsp;' }}</textarea>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding: 2px;">
                <div style="font-weight: bold;">Durchführung der Dienstreise</div>
                <table class="layout">
                    <tr>
                        <td style="width: 5cm; padding: 2px;">
                            <input type="checkbox" name="public_transport" value="1"/> <b>mit öffentl.
                                Verkehrsmitteln</b>
                        </td>
                        <td>
                            <input type="checkbox" name="db1" value="1"/> <b>Deutsche Bahn</b> - 1. Klasse
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 5cm; padding: 2px;"></td>
                        <td>
                            <input type="checkbox" name="db2" value="1"/> <b>Deutsche Bahn</b> - 2. Klasse
                        </td>
                    </tr>
                    <tr style="padding-bottom: 0.5em;">
                        <td style="width: 5cm; padding: 2px;"></td>
                        <td style="padding-left: 0.7cm;">
                            <input type="checkbox" name="bahncard" value="1"/> Bahncard vorhanden (siehe dazu Nr. 3 der
                            Hinweise zu Dienstreisen)
                        </td>
                    </tr>
                    <tr style="padding-bottom: 0.5em;">
                        <td style="width: 5cm; padding: 2px;"></td>
                        <td>
                            <input type="checkbox" name="vvs" value="1"/> <b>VVS</b> (Verkehrsverbund Stuttgart)
                        </td>
                    </tr>
                    <tr style="padding-bottom: 1em;">
                        <td style="width: 5cm; padding: 2px;"></td>
                        <td>
                            <input type="checkbox" name="plane" value="1"/> <b>Flugzeug</b> (Economy Class)
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 5cm; padding: 2px;">
                            <input type="checkbox" name="as_passenger" value="1"/> Als <b>Mitfahrer/in</b> im PKW bei:
                        </td>
                        <td>
                            <input type="text" name="passenger_name" value="" style="width: 10cm;"/>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;" colspan="2">
                            <input type="checkbox" name="own_car" value="1"/> Im <b>Privat-PKW</b>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <table class="layout">
                                <tr>
                                    <td colspan="2" style="padding: 2px 2px 2px 0.3cm;">
                                        Begründung bei Fahrt mit <b>privatem PKW</b> (zwingend erforderlich gem. § 1
                                        Abs. 1 a) – d) RKO):
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" style="padding: 2px 2px 2px 0.3cm;" valign="top">
                                        <table class="layout">
                                            <tr>
                                                <td style="width: 0.3cm;" valign="top">
                                                    <input type="checkbox" value="1" name="r1"/>
                                                </td>
                                                <td valign="top">
                                                    Dienstort mit öffentlichen Beförderungsmitteln
                                                    nicht oder nur schwer erreichbar
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" style="padding: 2px;" valign="top">
                                        <table class="layout">
                                            <tr>
                                                <td style="width: 0.3cm;" valign="top">
                                                    <input type="checkbox" value="1" name="r2"/>
                                                </td>
                                                <td valign="top">
                                                    Mitnahme mindestens einer Person aus dienstlichen Gründen
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" style="padding: 2px 2px 2px 0.3cm;" valign="top">
                                        <table class="layout">
                                            <tr>
                                                <td style="width: 0.3cm;" valign="top">
                                                    <input type="checkbox" value="1" name="r3"/>
                                                </td>
                                                <td valign="top">
                                                    erhebliche Zeitersparnis, dadurch Wahrnehmung
                                                    weiterer Dienstgeschäfte möglich
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" style="padding: 2px;" valign="top">
                                        <table class="layout">
                                            <tr>
                                                <td style="width: 0.3cm;" valign="top">
                                                    <input type="checkbox" value="1" name="r4"/>
                                                </td>
                                                <td valign="top">
                                                    Mitnahme von sperrigen oder schweren Arbeitsmitteln
                                                    aus dienstlichen Gründen
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <tr>
                                    <td width="50%" style="padding: 2px 2px 2px 0.3cm;" valign="top">
                                        <table class="layout">
                                            <tr>
                                                <td style="width: 0.3cm;" valign="top">
                                                    <input type="checkbox" value="1" name="r5"/>
                                                </td>
                                                <td valign="top">
                                                    Körper- oder Schwerbehindert
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                    <td width="50%" style="padding: 2px;" valign="top">
                                        <table class="layout">
                                            <tr>
                                                <td style="width: 0.3cm;" valign="top">
                                                    <input type="checkbox" value="1" name="r6"/>
                                                </td>
                                                <td valign="top">
                                                    <input type="text" name="r7" style="width: 8cm"/>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <table class="layout">
                    <tr>
                        <td colspan="3" style="padding: 2px; font-weight: bold;">Weitere Kosten</td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 2px;">
                            <input type="checkbox" value="1" name="conference_fee"/>
                            Tagungsbeitrag €:
                            <input type="text" name="conference_fee_amount" style="width: 2cm;"/>
                        </td>
                        <td width="30%" style="padding: 2px;">
                            <input type="checkbox" value="1" name="lump_sum"/>
                            Pauschalpreis €:
                            <input type="text" name="lump_sum_amount" style="width: 2cm;"/>
                        </td>
                        <td width="20%" style="padding: 2px;">
                            <input type="checkbox" value="1" name="food"/> einschl. Verpflegung
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 2px;">&nbsp;</td>
                        <td width="30%" style="padding: 2px;">&nbsp;</td>
                        <td width="20%" style="padding: 2px;">
                            <input type="checkbox" value="1" name="board"/> einschl. Übernachtung
                        </td>
                    </tr>
                    <tr>
                        <td width="50%" style="padding: 2px;">
                            <input type="checkbox" value="1" name="other_fee"/>
                            für
                            <input type="text" name="other_fee_amount" style="width: 2cm;"/> €:
                            <input type="text" name="other_fee_title" style="width: 5cm;"/>
                        </td>
                        <td width="30%" style="padding: 2px;">
                        </td>
                        <td width="20%" style="padding: 2px;">
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td width="50%" valign="top">
                <table class="layout">
                    <tr>
                        <td colspan="2" style="padding: 2px; height: 1.5cm;"  valign="top">
                            Ich habe die Hinweise zu Dienstreisen auf der Rückseite/2. Seite, insbesondere den Hinweis auf die
                            Ausschlussfrist (Nr. 3), zur Kenntnis genommen:
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;">
                            <input type="text" name="date1" value="{{ \Carbon\Carbon::now()->format('d.m.Y') }}" />
                        </td>
                        <td style="padding: 2px;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 2px; border-top: solid 1px black; ">Datum</td>
                        <td style="padding: 2px; border-top: solid 1px black; ">Unterschrift Antragsteller/in</td>
                    </tr>
                </table>
            </td>
            <td width="50%" valign="top">
                <table class="layout">
                    <tr>
                        <td colspan="2" style="padding: 2px; height: 1.5cm;" valign="top">
                            <span style="font-size: .8em;">- nur bei Bedarf -</span>&nbsp;<br/>
                            Nach Kenntnisnahme zur Genehmigung weitergeleitet
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 2px;">
                            <input type="text" name="date1" value="{{ \Carbon\Carbon::now()->format('d.m.Y') }}" />
                        </td>
                        <td style="padding: 2px;"></td>
                    </tr>
                    <tr>
                        <td style="padding: 2px; border-top: solid 1px black; ">Datum</td>
                        <td style="padding: 2px; border-top: solid 1px black; ">Unterschrift der weiterleitenden Person</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <h2> 2. Zur Dienstreisegenehmigung</h2>
    <table class="form" border="0" cellspacing="0" cellpadding="0">
        <tr>
            <td>
               <table class="layout">
                   <tr>
                       <td style="width: 3cm; padding: 2px;" valign="top">
                           Die Dienstreise wird
                       </td>
                       <td style="width: 3.5cm; padding: 2px;" valign="top">
                           <input type="checkbox" name="approved" value="1" /> genehmigt
                       </td>
                       <td style="width: 3.5cm; padding: 2px;" valign="top">
                           <input type="checkbox" name="approved" value="1" /> nicht genehmigt
                       </td>
                       <td></td>
                   </tr>
                   <tr>
                       <td valign="top"></td>
                       <td style="padding: 2px;" colspan="2" valign="top">
                           <input type="checkbox" name="fee_set1" value="1" /> Kilometervergütung nach  § 7 Abs. 2 und 3 RKO
                       </td>
                       <td style="padding: 2px;" valign="top">
                           <input type="checkbox" name="fee_set2" value="1" /> Kilometervergütung nach § 7 Abs. 4 RKO (0,16 €)
                       </td>
                   </tr>
                   <tr>
                       <td valign="top"></td>
                       <td style="padding: 2px;" colspan="3" valign="top">
                           <input type="checkbox" name="has_changes" value="1" /> mit folgenden Abweichungen genehmigt:
                       </td>
                   </tr>
                   <tr>
                       <td valign="top"></td>
                       <td style="padding: 2px; padding-left: 0.5cm;" colspan="3" valign="top">
                           <input type="checkbox" name="changed_transport" value="1" /> Verkehrsmittel geändert auf
                           <input type="text" name="changed_transport_to" value="" style="width: 10.5cm;" />
                       </td>
                   </tr>
                   <tr>
                       <td valign="top"></td>
                       <td style="padding: 2px; padding-left: 0.5cm;" colspan="3" valign="top">
                           <input type="checkbox" name="changed_board" value="1" /> ohne Übernachtungskosten (§ 10 Abs. 1 oder Abs. 3 RKO)
                       </td>
                   </tr>
                   <tr>
                       <td valign="top"></td>
                       <td style="padding: 2px; padding-left: 0.5cm;" colspan="3" valign="top">
                           <input type="checkbox" name="changed_other" value="1" />
                           <input type="text" name="changed_other_to" value="" style="width: 10.5cm;" />
                       </td>
                   </tr>
                   <tr>
                       <td style="padding: 1cm 2px 2px 2px;" valign="top">
                           <input type="text" name="date3" value="" style="width: 2.5cm;">
                       </td>
                       <td style="padding: 1cm 2px 2px 2px;" colspan="2" valign="top"></td>
                       <td></td>
                   </tr>
                   <tr>
                       <td style="padding: 2px; border-top: solid 1px black;" valign="top">
                           Datum
                       </td>
                       <td style="padding: 2px; border-top: solid 1px black;" colspan="2" valign="top">
                           Unterschrift der genehmigenden Person
                       </td>
                       <td></td>
                   </tr>
               </table>
            </td>
        </tr>
    </table>
    <h2> 3. Zurück an Antragsteller/in</h2>
</form>


<htmlpagefooter name="PageFooter">
    <div class="footer">
        <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
            <tr>
                <td width="20%">
                    OKR-ORG 6.4/5PC – 03/09
                </td>
                <td width="80%" style="text-align: right;">
                    Automatisch erstellt mit Pfarrplaner v{{ $version }} für {{ Auth::user()->name }}
                    ({{ \Carbon\Carbon::now()->setTimezone('Europe/Berlin')->format('d.m.Y H:i:s') }})
                </td>
            </tr>
        </table>
    </div>
</htmlpagefooter>

</body>
</html>
