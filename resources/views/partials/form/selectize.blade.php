<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <select class="form-control fancy-selectize @if(isset($class)){{ $class }}@endif" name="{{ $name }}" @if(false !== strpos($name, '[]')) multiple @endif>
        @if (isset($empty) && $empty)<option></option>@endif
        @foreach ($items as $item)
            <option value="{{ $item->id }}" @if(isset($value) && ($value->contains($item))) selected @endif>{{ $item->name }}</option>
        @endforeach
    </select>
</div>
