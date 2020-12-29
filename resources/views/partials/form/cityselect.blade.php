<div class="form-group">
    <?php if (isset($multiple) && isset($value) && $multiple && (!is_object($value) && (!is_array($value)))) $value = [$value]; ?>
    <label for="{{ $name }}">{{ $label }}</label>
    <select class="form-control fancy-selectize city-select @if(isset($class)){{ $class }}@endif" name="{{ $name }}" @if(isset($enabled) && (!$enabled)) disabled @endif @if(isset($multiple) && $multiple) multiple @endif >
        @foreach($cities->sortBy('name') as $city)
            <option value="{{$city->id}}"
                    @if (isset($value))
                        @if (isset($multiple) && $multiple)
                            @if (is_array($value)) @if (in_array($city->id, $value)) selected @endif @else
                            @if ($value->contains($city)) selected @endif @endif
                        @else
                            @if ($value->id == $city->id) selected @endif
                        @endif
                    @endif
            >
                {{$city->name}}
            </option>
        @endforeach
    </select>
    @include('partials.form.validation')
</div>
