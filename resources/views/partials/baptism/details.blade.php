<td>{{ $baptism->candidate_name }}<br />
    <small>
        {{ $baptism->candidate_address }}<br />
        {{ $baptism->candidate_zip }} {{ $baptism->candidate_city }}<br />
        Fon {{ $baptism->candidate_phone }}<br />
        E-Mail <a href="mailto:{{ $baptism->candidate_email }}">{{ $baptism->candidate_email }}</a>
    </small>
    @can('gd-kasualien-bearbeiten')
        @if ($baptism->commentsForCurrentUser->count() > 0)
            <span class="fa fa-comments"></span>&nbsp;{{ $baptism->commentsForCurrentUser->count() }}
        @endif
    @endcan

</td>
<td>
    {{ $baptism->first_contact_on ? $baptism->first_contact_on->format('d.m.Y') : '' }}<br />
    {{ $baptism->first_contact_with }}
</td>
<td>
    @if($baptism->appointment)
        @if($baptism->appointment->format('Ymd') < date('Ymd'))
            <span class="fa fa-check-circle"></span>
        @else
            <span class="fa fa-times-circle"></span>
        @endif
        &nbsp;{{$baptism->appointment->format('d.m.Y')}}
    @else
        <span class="fa fa-times-circle"></span>&nbsp;noch nicht vereinbart
    @endif
</td>
<td>
    @if ($baptism->registered) <span class="fa fa-check-circle"></span>&nbsp;Anmeldung erhalten
    <br />
    @if ($baptism->registration_document) <span class="fa fa-check-circle"></span>&nbsp;Formular erstellt @hasrole('Pfarrer*in') <a class="btn btn-sm btn-secondary" href="{{ env('APP_URL') }}storage/{{ $baptism->registration_document }}"><span class="fa fa-download" title="Formular als PDF-Datei herunterladen"></span></a>@endhasrole<br />
    @if ($baptism->signed) <span class="fa fa-check-circle"></span>&nbsp;Anmeldung unterzeichnet @else <span class="fa fa-times-circle"></span>&nbsp;noch nicht unterzeichnet @endif<br />
    @endif

    @else <span class="fa fa-times-circle"></span>&nbsp;Anmeldung noch nicht erhalten @endif

</td>
<td>
    @if ($baptism->docs_ready)
        <span class="fa fa-check-circle"></span>&nbsp;erstellt
        @if ($baptism->docs_where) <br /><span class="fa fa-check-circle"></span>&nbsp;Hinterlegt: {{ $baptism->docs_where }} @endif
    @else <span class="fa fa-times-circle"></span>&nbsp;noch nicht erstellt @endif
</td>
