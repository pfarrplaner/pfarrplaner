<div class="form-group @if(isset($required) && ($required)) required @endif" @if(isset($id)) id="{{ $id }}" @endif>
    @if($label)<label for="{{ $name }}">{{ $label }}</label>@endif
    <input
            type="@if(isset($type)){{ $type }}@else{{'text'}}@endif"
            class="form-control @if(isset($class)) {{ $class }}@endif @if ($errors->has($name))is-invalid @endif"
            name="{{ $name }}" @if(isset($id)) id="{{ $id }}_input" @endif
            @if($errors->any()) value="{{ old($name) }}" @else @if(isset($value))value="{{ $value }}" @endif @endif
            @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif
            @if(isset($enabled) && (!$enabled)) disabled @endif
            @if(isset($required) && ($required)) required @endif
            @if(isset($autofocus) && ($autofocus)) autofocus @endif
            @if(isset($pattern)) pattern="{{ $pattern }}" @endif
    />
    @include('partials.form.validation')
</div>
