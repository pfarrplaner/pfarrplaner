@foreach(['success', 'error', 'warning', 'info'] as $class)
    <?php
        $alertClass=['success' => 'success', 'error' => 'danger', 'warning' => 'warning', 'info' => 'info'];
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
            <div class="alert alert-{{ $alertClass[$class] }} alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                {!! $message !!}
            </div>
        @endforeach
    @endif
@endforeach

@if ($errors->any())
    <div class="alert alert-danger alert-block">
        <button type="button" class="close" data-dismiss="alert">×</button>
        Bitte überprüfe deine Eingaben:<br />
        <small><ul>
            @foreach($errors->all() as $message)
                <li>{!! $message !!}</li>
            @endforeach
            </ul>
        </small>
    </div>
@endif
