<div class="form-group">
    <label style="display:block;">{{ $label }}</label>
    @if(!isset($preventAutoSet)) @php
        $value = $value ?? array_values($items)[0];
    @endphp @endif
    @foreach ($items as $title => $key)
    <div class="form-check-inline">
        <input type="radio" name="{{ $name }}" value="{{ $key }}" autocomplete="off" @if(isset($value) && ($value == $key))checked @endif @if(isset($enabled) && (!$enabled)) disabled @endcannot />
        &nbsp;<label class="form-check-label">
            {{ $title }}
        </label>
    </div>
    @endforeach
    @include('partials.form.validation')
</div>
