@foreach($service->weddings as $wedding)
    <h5 class="text-center"
        style="margin-top: 0; margin-bottom: 0; font-weight: 500; color: inherit; vertical-align: baseline; font-size: 20px; line-height: 24px;"
        align="center"><strong>Trauung von {{ $wedding->spouse1_name }} und {{ $wedding->spouse2_name }}</strong></h5>
    <p class="text-center" style="line-height: 24px; font-size: 16px; margin: 0;" align="center"><small>&nbsp;</small>
    </p>
    <table class="table" border="0" cellpadding="0" cellspacing="0"
           style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%; max-width: 100%;"
           bgcolor="#ffffff">
        <tbody>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="left" valign="top">Traugespräch
            </td>
            <td class="text-right"
                style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="right"
                valign="top">@if($wedding->appointment){{ $wedding->appointment->format('d.m.Y') }} {{ $wedding->appointment <= \Carbon\Carbon::now()? '✔' : '✘' }} @else
                    ✘ @endif</td>
        </tr>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="left" valign="top">Anmeldung erhalten
            </td>
            <td class="text-right"
                style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="right" valign="top">{{ $wedding->registered ? '✔' : '✘' }}</td>
        </tr>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="left" valign="top">Formular erstellt
            </td>
            <td class="text-right"
                style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="right" valign="top">{{ $wedding->registration_document ? '✔' : '✘' }}</td>
        </tr>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="left" valign="top">Anmeldung unterschrieben
            </td>
            <td class="text-right"
                style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="right" valign="top">{{ $wedding->signed ? '✔' : '✘' }}</td>
        </tr>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="left" valign="top">Urkunden erstellt
            </td>
            <td class="text-right"
                style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="right" valign="top">{{ $wedding->docs_ready ? '✔' : '✘' }}</td>
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Dateianhänge @endcomponent
            @component('mail.layout.blocks.cell')
                @if(count($wedding->attachments))
                    @foreach($wedding->attachments as $attachment)
                        @if(Storage::exists($attachment->file))
                            <a href="{{ route('attachment', $attachment->id) }}" class="btn-secondary btn-sm"
                               title="{{ $attachment->title }}, {{ \App\Helpers\FileHelper::bytesToHuman(Storage::size($attachment->file)) }}, {{ Storage::mimeType($attachment->file) }}">
                                <span class="fa {{ \App\Helpers\FileHelper::icon($attachment->file) }}"></span>
                            </a>
                        @endif
                    @endforeach
                @endif
            @endcomponent
        </tr>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;"
                align="left" valign="top" colspan="2">
                <a style="padding: 5px; border-radius: 5px; background-color: #a61380; color: white;"
                   href="{{ route('weddings.edit', $wedding) }}">Trauung im Pfarrplaner öffnen</a>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="hr " style="width: 100%; margin: 20px 0; border: 0;">
        <table border="0" cellpadding="0" cellspacing="0"
               style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%;">
            <tbody>
            <tr>
                <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #dddddd; border-top-style: solid; height: 1px; width: 100%; margin: 0;"
                    align="left"></td>
            </tr>
            </tbody>
        </table>
    </div>
@endforeach
