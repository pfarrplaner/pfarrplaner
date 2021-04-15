@php

/**
* Handle locations grouped or ungrouped
* Fallback: if locations are not in a categorized array, create a "foo" category
*/

    if (!isset($grouped) || (!$grouped)) {
        $grouped = false;
        $allLocations['foo'] = $locations;
    } else {
        $allLocations = $locations;
    }

@endphp
<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <select class="form-control location-select @if(isset($class)){{ $class }}@endif" name="{{ $name }}" @if(isset($enabled) && (!$enabled)) disabled @endcannot @if(substr($name, -2)=='[]') multiple @endif >
        @if(isset($special) && ($special != ''))<option data-time="" value="{{ $special }}" selected>{{ $special }}</option>
            @else <option></option>
        @endif
        @foreach ($allLocations as $groupTitle => $theseLocations)
            @if($grouped)<optgroup label="{{ $groupTitle }}">@endif
            @foreach($theseLocations->sortBy('name') as $thisLocation)
                <option data-time="{{ strftime('%H:%M', strtotime($thisLocation->default_time)) }}"
                        value="{{$thisLocation->id}}"
                        @if (isset ($value) && (is_object($value)))
                            @if ($value->id == $thisLocation->id) selected @endif
                        @elseif(isset($value) && is_array($value))
                            @if(in_array($thisLocation->id, $value)) selected @endif
                        @endif
                >
                    {{$thisLocation->name}}
                </option>
            @endforeach
            @if($grouped)</optgroup>@endif
        @endforeach
    </select>
    @include('partials.form.validation')
</div>
