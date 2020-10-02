<div class="form-group">
    <label for="{{ $name }}">{{ $label }}</label>
    <textarea rows="@if(isset($rows)){{ $rows }}@else 5 @endif" class="form-control @if(isset($class)){{ $class }} @endif" name="{{ $name }}">@if(isset($value)){{ $value }}@endif</textarea>
    @include('partials.form.validation')
</div>
