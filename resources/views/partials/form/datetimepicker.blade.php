<div class="form-group @if(isset($required) && ($required)) required @endif" @if(isset($id)) id="{{ $id }}" @endif>
    @if($label)<label for="{{ $name }}">{{ $label }}</label>@endif
    <input
            type="@if(isset($type)){{ $type }}@else{{'text'}}@endif"
            class="form-control datetimepicker datetimepicker-input @if(isset($class)) {{ $class }}@endif @if ($errors->has($name))is-invalid @endif"
            name="{{ $name }}" @if(isset($id)) id="{{ $id }}_input" @else id="{{ $name }}" @endif
            @if($errors->any()) value="{{ old($name) }}" @else @if(isset($value))value="{{ $value }}" @endif @endif
            @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif
            @if(isset($enabled) && (!$enabled)) disabled @endif
            @if(isset($required) && ($required)) required @endif
            @if(isset($pattern)) pattern="{{ $pattern }}" @endif
            data-toggle="datetimepicker" @if(isset($id)) data-target="#{{ $id }}_input" @else data-target="#{{ $name }}" @endif
    />
    @if($errors->has($name))
        @foreach($errors->get($name) as $message)
        <div class="invalid-feedback">{!! $message !!}</div>
        @endforeach
    @endif
</div>
