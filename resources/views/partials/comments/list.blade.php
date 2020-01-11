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

    </script>
</div>
