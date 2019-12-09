<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <select class="form-control fancy-selectize @if(isset($class)){{ $class }}@endif" name="{{ $name }}" @if(false !== strpos($name, '[]')) multiple @endif>
        @if (isset($empty) && $empty)<option></option>@endif
        @foreach ($items as $item)
            <option @if(isset($item->id))value="{{ $item->id }}"@endif @if(isset($value) && ((is_object($value) && ($value->contains($item))) || ($value == $item->id))) selected @endif>@if(isset($item->name)){{ $item->name }}@else{{$item}}@endif</option>
        @endforeach
    </select>
</div>
