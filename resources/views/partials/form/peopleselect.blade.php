<div class="form-group" @if(isset($id)) id="{{ $id }}" @endif>
    @if($label)<label for="{{ $name }}"><span class="fa fa-user"></span>&nbsp;{{ $label }}</label>@endif
    <select class="form-control peopleSelect @if(isset($class)){{ $class }} @endif" name="{{ $name }}" multiple @if(isset($enabled) && (!$enabled)) disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" @if(isset($id)) id="{{ $id }}_input" @endif>
        @foreach ($people as $user)
            <option value="{{ $user->id }}"
                    @if(isset($useItemId) && $useItemId)
                        @if(isset($value) && $value->pluck('id')->contains($user->id)) selected @endif
                    @else
                        @if(isset($value) && $value->contains($user)) selected @endif
                    @endif
            >{{ $user->name }}</option>
        @endforeach
    </select>
</div>
