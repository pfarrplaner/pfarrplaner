<div class="form-group" @if(isset($id)) id="{{ $id }}" @endif>
    @if($label)<label for="{{ $name }}"><span class="fa fa-user"></span>&nbsp;{{ $label }}</label>@endif
    <select class="form-control @if(isset($class)){{ $class }} @endif" name="{{ $name }}" @if(false !== strpos($name, '[]')) multiple @endif @if(isset($enabled) && (!$enabled)) disabled @endcannot @if(isset($id)) id="{{ $id }}_input" @endif>
        @foreach ($items as $item)
            @if(isset($empty) && $empty)<option></option>@endif
            <option value="@if(isset($strings) && $strings){{ $item }}@else{{ $item->id }}@endif" @if(isset($value) && $value->contains($item)) selected @endif>@if(isset($strings) && $strings){{ $item }}@else{{ $item->name }}@endif</option>
        @endforeach
    </select>
</div>
