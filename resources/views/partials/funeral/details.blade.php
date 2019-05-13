<td>{{$funeral->buried_name}}
    @can('gd-kasualien-bearbeiten')
        @if ($funeral->commentsForCurrentUser->count() > 0)
            <span class="fa fa-comments"></span>&nbsp;{{ $funeral->commentsForCurrentUser->count() }}
        @endif
    @endcan

</td>
<td>{{$funeral->type}}</td>
<td>{{$funeral->announcement ? $funeral->announcement->format('d.m.Y') : ''}}</td>
