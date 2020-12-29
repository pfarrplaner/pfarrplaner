@foreach($participants as $participant)
    <span>@include('calendar.partials.name', ['person' => $participant])</span>
    @if($loop->last) @else | @endif
@endforeach
