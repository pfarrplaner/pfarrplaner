<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <select class="form-control @if(isset($class)) {{ $class }} @endif" name="{{ $name }}" @if(isset($enabled) && (!$enabled)) disabled @endif>
        @foreach($days as $thisDay)
            <option value="{{$thisDay->id}}"
                    @if (isset($value) && ($value->id == $thisDay->id)) selected @endif
            >{{$thisDay->date->format('d.m.Y')}}</option>
        @endforeach
    </select>
    @include('partials.form.validation')
</div>
