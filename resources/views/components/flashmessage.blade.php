@foreach(['success', 'error', 'warning', 'info'] as $class)
    <?php
    if (Session::get($class)) {
        $messages = Session::get($class);
        Session::remove($class);
        $messages = is_array($messages) ? $messages : [$messages];
    } else {
        $messages = false;
    }
    ?>
    @if ($messages)
        @foreach($messages as $message)
            <div class="alert alert-{{ $class }} alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {!! $message !!}
            </div>
        @endforeach
    @endif
@endforeach

@if ($errors->any())
    <div class="alert alert-danger">
        <button type="button" class="close" data-dismiss="alert">×</button>
        Bitte überprüfe deine Eingaben:
    </div>
@endif
