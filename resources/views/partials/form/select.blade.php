<div class="form-group" @if(isset($id)) id="{{ $id }}" @endif>
    @if($label)<label for="{{ $name }}">{{ $label }}</label>@endif
    <select class="form-control @if(isset($class)){{ $class }} @endif" name="{{ $name }}" @if(false !== strpos($name, '[]')) multiple @endif @if(isset($enabled) && (!$enabled)) disabled @endcannot @if(isset($id)) id="{{ $id }}_input" @endif>
        @foreach ($items as $item)
            @if(isset($empty) && $empty)<option></option>@endif
                @if((strpos($name, '[]') !== false))
                    <option @if(isset($item->id))value="{{ $item->id }}" @endif @if(isset($value) && ($value->contains($item))) selected @endif>@if(isset($item->name)){{ $item->name }}@else{{$item}}@endif</option>
                @else
                    <?php $checkValue = is_object($item) ? $item->id : $item; ?>
                    <option @if(is_object($item) && isset($item->id))value="{{ $item->id }}" @endif @if(isset($value) && ($value == $checkValue)) selected @endif>@if(isset($item->name)){{ $item->name }}@else{{$item}}@endif</option>
                @endif
        @endforeach
    </select>
</div>
