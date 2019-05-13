<tr data-comment-id="{{ $comment->id }}" class="commentRow">
    <td>
        @if($comment->private) <span class="badge badge-danger"><span class="fa fa-lock" title="Nur du kannst diesen Kommentar sehen"></span></span>&nbsp;@endif <b>{{ $comment->user->name }}</b><br />
        <small>
            {{ $comment->created_at->format('d.m.Y, H:i') }} Uhr
            @if ($comment->created_at != $comment->updated_at)
                (zuletzt geändert: {{ $comment->updated_at->format('d.m.Y, H:i') }} Uhr)
            @endif
        </small>
        <hr />
        {{ $comment->body }}
        @if ($comment->user_id == \Illuminate\Support\Facades\Auth::user()->id)
            <br />
            <button class="btn btn-sm btn-secondary btnEditComment" title="Kommentar bearbeiten" data-comment-id="{{ $comment->id }}" data-route="{{ route('comments.edit', $comment->id) }}"><span class="fa fa-edit"></span></button>
            <button class="btn btn-sm btn-danger btnDeleteComment" title="Kommentar löschen" data-comment-id="{{ $comment->id }}" data-route="{{ route('comments.destroy', $comment->id) }}"><span class="fa fa-trash"></span></button>
        @endif
    </td>
</tr>
