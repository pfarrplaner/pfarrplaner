<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <select class="form-control fancy-selectize @if(isset($class)){{ $class }}@endif" name="{{ $name }}" @if(false !== strpos($name, '[]')) multiple @endif>
        @if (isset($empty) && $empty)<option></option>@endif
        @foreach ($items as $item)
            @if(strpos($name, '[]') !== false)
                <option @if(isset($item->id))value="{{ $item->id }}" @endif @if(isset($value) && ($value->contains($item))) selected @endif>@if(isset($item->name)){{ $item->name }}@else{{$item}}@endif</option>
            @else
                <option @if(isset($item->id))value="{{ $item->id }}" @endif @if(isset($value) && ($value == $item->id)) selected @endif>@if(isset($item->name)){{ $item->name }}@else{{$item}}@endif</option>
            @endif
        @endforeach
    </select>
</div>
