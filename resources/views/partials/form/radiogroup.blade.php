<div class="form-group">
    <label style="display:block;">{{ $label }}</label>
    @foreach ($items as $title => $key)
    <div class="form-check-inline">
        <input type="radio" name="{{ $name }}" value="{{ $key }}" autocomplete="off" @if(isset($value) && ($value == $key))checked @endif @if(isset($enabled) && (!$enabled)) disabled @endcannot />
        &nbsp;<label class="form-check-label">
            {{ $title }}
        </label>
    </div>
    @endforeach
</div>
