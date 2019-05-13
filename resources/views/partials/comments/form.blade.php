<tr @if(null == $comment)id="newCommentRow"@else data-comment-id="{{ $comment->id }}" data-update-route="{{ route('comments.update', $comment) }}"@endif>
    <td>
        <b>@if(null == $comment)Neuen Kommentar hinzufügen @else Kommentar bearbeiten @endif</b>
        <hr />
        <div class="check">
            <input type="checkbox" class="editCommentPrivate" @if ((null !== $comment) && ($comment->private)) checked @endif />
            <label><span class="fa fa-lock"></span>&nbsp;Dieser Kommentar soll nur für mich sichtbar sein.</label>
        </div>
        <div class="form-group">
            <textarea id="newCommentBody" class="form-control editCommentBody" placeholder="Dein Kommentar...">@if(null !== $comment){{ $comment->body }}@endif</textarea>
        </div>
        <div class="form-group">
            @if(null == $comment)
            <button id="newCommentSave" class="btn btn-sm btn-secondary">Kommentar speichern</button>
            @else
                <button class="btn btn-sm btn-secondary btnEditCommentSave">Kommentar speichern</button>
            @endif
        </div>
    </td>
</tr>
