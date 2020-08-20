<div class="form-group @if(isset($required) && ($required)) required @endif" @if(isset($id)) id="{{ $id }}" @endif>
    @if($label)<label for="{{ $name }}">{{ $label }}</label>@endif
    @if(isset($value))
        @if(Storage::exists($value))
                <br />
                <small>
                    Vorhandener Dateianhang:<br />
                    <div class="row">
                        <div class="col-sm-8">
                            <span class="fa {{ \App\Helpers\FileHelper::icon($value) }}"></span>
                            {{ Storage::mimeType($value) }},
                            {{ \App\Helpers\FileHelper::bytesToHuman(Storage::size($value)) }},
                            {{ \Carbon\Carbon::createFromTimestamp(Storage::lastModified($value))->format('d.m.Y H:i:s') }}
                            (<a href="{{ route('storage', ['path' => pathinfo($value, PATHINFO_FILENAME), 'prettyName' => ($prettyName ?? $name).'.'.(isset($forceExtension) ? $forceExtension :  pathinfo($value, PATHINFO_EXTENSION))]) }}">Ansehen</a>)<br />
                        </div>
                        <div class="col-sm-4 text-right">
                            <label><input type="checkbox" name="remove_{{ $name }}" value="1" /> Entfernen</label>
                        </div>
                    </div>
                </small>
        @endif
    @endif
    <input
            type="file"
            class="form-control @if(isset($class)) {{ $class }}@endif @if ($errors->has($name))is-invalid @endif"
            name="{{ $name }}" @if(isset($id)) id="{{ $id }}_input" @endif
            @if($errors->any()) value="{{ old($name) }}" @else @if(isset($value))value="{{ $value }}" @endif @endif
            @if(isset($placeholder)) placeholder="{{ $placeholder }}" @endif
            @if(isset($accept)) accept="{{ $accept }}" @endif
            @if(isset($enabled) && (!$enabled)) disabled @endif
            @if(isset($required) && ($required)) required @endif
            @if(isset($pattern)) pattern="{{ $pattern }}" @endif
    />
    @include('partials.form.validation')
</div>
