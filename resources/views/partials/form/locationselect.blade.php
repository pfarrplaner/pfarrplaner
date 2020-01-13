<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <select class="form-control fancy-selectize @if(isset($class)){{ $class }}@endif" name="{{ $name }}" @if(isset($enabled) && (!$enabled)) disabled @endcannot >
        @foreach($locations as $thisLocation)
            <option data-time="{{ strftime('%H:%M', strtotime($thisLocation->default_time)) }}"
                    value="{{$thisLocation->id}}"
                    @if (isset ($value) && (is_object($value)))
                        @if ($value->id == $thisLocation->id) selected @endif
                    @endif
            >
                {{$thisLocation->name}}
            </option>
        @endforeach
        <option value="0"
                @if ((!isset($value)) || ($value == null)) selected @endif
        >Freie Ortsangabe</option>
    </select>
</div>
