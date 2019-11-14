<div class="form-check">
    <input class="form-check-input" type="checkbox" name="{{ $name }}" value="1"
           @if (isset($id)) id="{{ $id }}"@endif @if (isset($value) && $value) checked @endif @if(isset($enabled) && (!$enabled)) disabled @endcannot >
    <label class="form-check-label" for="{{ $name }}">
        {{ $label }}
    </label>
    <br /><br />
</div>
