@can('update', $service)
    @if($service->commentsForUser($user)->get()->count())
        <table class="card w-100 " border="0" cellpadding="0" cellspacing="0" style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: separate !important; border-radius: 4px; width: 100%; overflow: hidden; border: 1px solid #dee2e6;" bgcolor="#ffffff">
            <tbody>
            <tr>
                <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; width: 100%; margin: 0;" align="left">
                    <div>
                        <table class="card-body" border="0" cellpadding="0" cellspacing="0" style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%;">
                            <tbody>
                            <tr>
                                <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;" align="left">
                                    <div>
                                        <h4 class="text-center" style="margin-top: 0; margin-bottom: 0; font-weight: 500; color: inherit; vertical-align: baseline; font-size: 24px; line-height: 28.8px;" align="center">Kommentare zu diesem diesem Gottesdienst</h4>

                                        <div class="hr " style="width: 100%; margin: 20px 0; border: 0;">
                                            <table border="0" cellpadding="0" cellspacing="0" style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%;">
                                                <tbody>
                                                <tr>
                                                    <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #dddddd; border-top-style: solid; height: 1px; width: 100%; margin: 0;" align="left"></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <table class="table" border="0" cellpadding="0" cellspacing="0" style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%; max-width: 100%;" bgcolor="#ffffff">
                                            <thead>
                                            </thead>
                                            <tbody>
                                            @foreach($service->commentsForUser($user)->get() as $comment)
                                                <tr>
                                                    <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 1px; border-top-color: #e9ecef; border-top-style: solid; margin: 0; padding: 12px;" align="left" valign="top">
                                                        <b>{{ $comment->user->name }}</b> @if($comment->private)(private Notiz)@endif<br />
                                                        <small>
                                                            {{ $comment->created_at->format('d.m.Y, H:i') }} Uhr
                                                            @if ($comment->created_at != $comment->updated_at)
                                                                (zuletzt geändert: {{ $comment->updated_at->format('d.m.Y, H:i') }} Uhr)
                                                            @endif
                                                        </small>
                                                        <br /><br />
                                                        {{ $comment->body }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </td>
            </tr>
            </tbody>
        </table>
        <table class="s-4 w-100" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
            <tbody>
            <tr>
                <td height="24" style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 24px; width: 100%; height: 24px; margin: 0;" align="left">

                </td>
            </tr>
            </tbody>
        </table>
    @endif
@endcan
