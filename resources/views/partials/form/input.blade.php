<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <input type="text" class="form-control @if(isset($class)) {{ $class }}@endif" name="{{ $name }}" @if(isset($value)) value="{{ $value }}" @endif @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif @if(isset($enabled) && (!$enabled)) disabled @endif/>
</div>
