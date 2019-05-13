<div class="table-responsive">
    <table class="table table-striped">
        <thead>

        </thead>
        <tbody>
        @foreach($owner->commentsForCurrentUser as $comment)
            @include('partials.comments.single')
        @endforeach
        @include('partials.comments.form', ['comment' => NULL])
        </tbody>
    </table>
    <script>
        function updateCommentCount() {
            $('#commentCount').html($('.commentRow').length);
        }

        function commentEditButtons() {
            $('.btnEditComment').click(function(event){
                event.preventDefault();
                axios.get($(this).data('route'), {
                   id: $(this).data('comment-id'),
                }).then((response) => {
                    console.log(response);
                    $(this).closest('tr').replaceWith(response.data);
                    commentEditButtons();
                }).catch((error)=>{
                    console.log(error.response.data)
                });
            });

            $('.btnEditCommentSave').click(function(event){
                event.preventDefault();
                var tr = $(this).closest('tr');
                axios.patch(tr.data('update-route'), {
                    id: tr.data('comment-id'),
                    'commentable_id': '{{ $owner->id }}',
                    'commentable_type': '{!! addslashes($ownerClass) !!}',
                    'body': tr.find('.editCommentBody').first().val(),
                    'private': tr.find('.editCommentPrivate').first().prop('checked'),
                }).then((response) => {
                    console.log(response);
                    $(this).closest('tr').replaceWith(response.data);
                    commentEditButtons();
                    setDirty();
                }).catch((error)=>{
                    console.log(error.response.data)
                });
            });


            $('.btnDeleteComment').click(function(){
                event.preventDefault();
                axios.delete($(this).data('route'), {
                    id: $(this).data('comment-id'),
                }).then((response) => {
                    console.log(response);
                    $(this).closest('tr').remove();
                    updateCommentCount();
                    setDirty();
                });
            });
        }

        $(document).ready(function(){
            $('#newCommentSave').click(function(event){
                event.preventDefault();

                axios.post('{{ route('comments.store') }}', {
                    'commentable_id': '{{ $owner->id }}',
                    'commentable_type': '{!! addslashes($ownerClass) !!}',
                    'body': $('#newCommentRow .editCommentBody').first().val(),
                    'private': $('#newCommentRow .editCommentPrivate').first().prop('checked'),
                })
                .then((response)=>{
                    console.log(response);
                    $('#newCommentRow').before(response.data);
                    updateCommentCount();
                    setDirty();
                    $('#newCommentRow .editCommentBody').first().val('');
                    $('#newCommentRow .editCommentPrivate').first().prop('checked', false);
                    commentEditButtons();
                }).catch((error)=>{
                    console.log(error.response.data)
                });
            });

            commentEditButtons();
        });


    </script>
</div>
