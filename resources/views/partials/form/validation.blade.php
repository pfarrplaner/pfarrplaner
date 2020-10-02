@if($errors->has($name))
    @foreach($errors->get($name) as $message)
        <div class="invalid-feedback">{!! $message !!}</div>
    @endforeach
@endif
@if(isset($invalid))
    <div class="invalid-feedback">{!! $invalid !!}</div>
@endif
@if(isset($valid))
    <div class="invalid-feedback">{!! $valid !!}</div>
@endif
