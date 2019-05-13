<td>{{ $wedding->spouse1_name }}@if($wedding->spouse1_birth_name) ({{$wedding->spouse1_birth_name}}) @endif<br />
    <small>
        Fon {{ $wedding->spouse1_phone }}<br />
        E-Mail <a href="mailto:{{ $wedding->spouse1_email }}">{{ $wedding->spouse1_email }}</a>
    </small>
    @can('gd-kasualien-bearbeiten')
        @if ($wedding->commentsForCurrentUser->count() > 0)
            <span class="fa fa-comments"></span>&nbsp;{{ $wedding->commentsForCurrentUser->count() }}
        @endif
    @endcan
</td>
<td>{{ $wedding->spouse2_name }}@if($wedding->spouse2_birth_name) ({{$wedding->spouse2_birth_name}}) @endif<br />
    <small>
        Fon {{ $wedding->spouse2_phone }}<br />
        E-Mail <a href="mailto:{{ $wedding->spouse2_email }}">{{ $wedding->spouse2_email }}</a>
    </small>
</td>
<td>
    @if($wedding->appointment)
        @if($wedding->appointment->format('Ymd') < date('Ymd'))
            <span class="fa fa-check-circle"></span>
        @else
            <span class="fa fa-times-circle"></span>
        @endif
        &nbsp;{{$wedding->appointment->format('d.m.Y')}}
    @else
        <span class="fa fa-times-circle"></span>&nbsp;noch nicht vereinbart
    @endif
</td>
<td>
    @if ($wedding->registered) <span class="fa fa-check-circle"></span>&nbsp;Anmeldung erhalten
    <br />
    @if ($wedding->registration_document) <span class="fa fa-check-circle"></span>&nbsp;Formular erstellt @hasrole('Pfarrer*in') <a class="btn btn-sm btn-secondary" href="{{ env('APP_URL') }}storage/{{ $wedding->registration_document }}"><span class="fa fa-download" title="Formular als PDF-Datei herunterladen"></span></a> @endhasrole<br />
    @if ($wedding->signed) <span class="fa fa-check-circle"></span>&nbsp;Anmeldung unterzeichnet @else <span class="fa fa-times-circle"></span>&nbsp;noch nicht unterzeichnet @endif<br />
    @endif

    @else <span class="fa fa-times-circle"></span>&nbsp;Anmeldung noch nicht erhalten @endif

</td>
<td>
    @if ($wedding->docs_ready)
        <span class="fa fa-check-circle"></span>&nbsp;erstellt
        @if ($wedding->docs_where) <br /><span class="fa fa-check-circle"></span>&nbsp;Hinterlegt: {{ $wedding->docs_where }} @endif
    @else <span class="fa fa-times-circle"></span>&nbsp;noch nicht erstellt @endif
</td>
