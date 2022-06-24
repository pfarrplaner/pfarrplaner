@component('mail::message')
# Exception: {{ $flat->getMessage() }}

# Stacktrace
@component('mail::panel')
````
{!!  $flat->getTraceAsString() !!}
````
@endcomponent


## Context

@foreach($report['context'] as $title => $data)

### {{ ucfirst(str_replace('_', ' ', $title)) }}

@component('mail::table')
| <!-- -->    | <!-- -->    |
|-------------|-------------|
@foreach($data as $key => $value)| {{ $key }} | @if($value)````@if(is_array($value)){!! json_encode($value) !!}@else{{ $value }}@endif````@endif |
@endforeach
@endcomponent
@endforeach

@endcomponent
