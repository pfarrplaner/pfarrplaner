<td>{{$funeral->buried_name}}
    @can('gd-kasualien-bearbeiten')
        @if ($funeral->commentsForCurrentUser->count() > 0)
            <span class="fa fa-comments"></span>&nbsp;{{ $funeral->commentsForCurrentUser->count() }}
        @endif
    @endcan
    @if ((null !== $funeral->dob) && (null !== $funeral->dod))<br /><small>
        {{ $funeral->dob->format('d.m.Y') }}-{{ $funeral->dod->format('d.m.Y') }} ({{ $funeral->age() }})
    </small> @endif

</td>
<td>{{$funeral->type}}</td>
<td>
    @if($funeral->appointment)
        @if($funeral->appointment->format('Ymd') < date('Ymd'))
            <span class="fa fa-check-circle"></span>
        @else
            <span class="fa fa-times-circle"></span>
        @endif
        &nbsp;{{$funeral->appointment->format('d.m.Y, H:i')}} Uhr
        <br />
        <a class="btn btn-sm btn-secondary" href="{{ route('funeral.appointment.ical', $funeral) }}" title="Zu Outlook hinzufügen"><span class="fa fa-calendar-alt"></span></a>
    @else
        <span class="fa fa-times-circle"></span>&nbsp;noch nicht vereinbart
    @endif
</td>
<td>{{$funeral->announcement ? $funeral->announcement->format('d.m.Y') : ''}}</td>
