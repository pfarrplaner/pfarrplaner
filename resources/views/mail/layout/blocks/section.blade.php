<h5 class="text-center" style="margin-top: 0; margin-bottom: 0; font-weight: 500; color: inherit; vertical-align: baseline; font-size: 20px; line-height: 24px;" align="center"><strong>{{ $title }}</strong></h5>
<p class="text-center" style="line-height: 24px; font-size: 16px; margin: 0;" align="center">{{ $subtitle }}</p>
<table class="table" border="0" cellpadding="0" cellspacing="0" style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%; max-width: 100%;" bgcolor="#ffffff">
    <thead>
    <tr>
        {{ $header }}
    </tr>
    </thead>
    <tbody>
    {{ $slot }}
    </tbody>
</table>
