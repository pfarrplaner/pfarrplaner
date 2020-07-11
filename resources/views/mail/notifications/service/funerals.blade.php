@foreach($service->funerals as $funeral)
    <h5 class="text-center" style="margin-top: 0; margin-bottom: 0; font-weight: 500; color: inherit; vertical-align: baseline; font-size: 20px; line-height: 24px;" align="center"><strong>Trauerfeier für {{ $funeral->buried_name }}</strong></h5>
    <p class="text-center" style="line-height: 24px; font-size: 16px; margin: 0;" align="center"><small>{{ $funeral->buried_address }} &middot; {{ $funeral->buried_zip }} {{ $funeral->buried_city }}</small></p>
    <table class="table" border="0" cellpadding="0" cellspacing="0" style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%; max-width: 100%;" bgcolor="#ffffff">
        <tbody>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;" align="left" valign="top">Abkündigung</td>
            <td class="text-right" style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;" align="right" valign="top">@if($funeral->announcement){{ $funeral->announcement->format('d.m.Y') }} {{ $funeral->announcement <= \Carbon\Carbon::now()? '✔' : '✘' }} @else ✘ @endif</td>
        </tr>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;" align="left" valign="top">Bestattungsart</td>
            <td class="text-right" style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;" align="right" valign="top">{{ $funeral->type }}</td>
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Dateianhänge @endcomponent
            @component('mail.layout.blocks.cell')
                @if(count($funeral->attachments))
                    @foreach($funeral->attachments as $attachment)
                        @if(Storage::exists($attachment->file))
                            <a href="{{ route('attachment', $attachment->id) }}"  class="btn-secondary btn-sm"
                               title="{{ $attachment->title }}, {{ \App\Helpers\FileHelper::bytesToHuman(Storage::size($attachment->file)) }}, {{ Storage::mimeType($attachment->file) }}">
                                <span class="fa {{ \App\Helpers\FileHelper::icon($attachment->file) }}"></span>
                            </a>
                        @endif
                    @endforeach
                @endif
            @endcomponent
        </tr>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;" align="left" valign="top" colspan="2">
                <a style="padding: 5px; border-radius: 5px; background-color: #a61380; color: white;" href="{{ route('funerals.edit', $funeral) }}">Bestattung im Pfarrplaner öffnen</a>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="hr " style="width: 100%; margin: 20px 0; border: 0;">
        <table border="0" cellpadding="0" cellspacing="0" style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%;">
            <tbody>
            <tr>
                <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #dddddd; border-top-style: solid; height: 1px; width: 100%; margin: 0;" align="left"></td>
            </tr>
            </tbody>
        </table>
    </div>
@endforeach
