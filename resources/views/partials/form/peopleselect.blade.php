<div class="form-group">
    <label for="{{ $name }}"><span class="fa fa-user"></span>&nbsp;{{ $label }}</label>
    <select class="form-control peopleSelect @if(isset($class)){{ $class }} @endif" name="{{ $label }}" multiple @if(isset($enabled) && (!$enabled)) disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
        @foreach ($people as $user)
            <option value="{{ $user->id }}" @if(isset($value) && $value->contains($user)) selected @endif>{{ $user->name }}</option>
        @endforeach
    </select>
</div>
