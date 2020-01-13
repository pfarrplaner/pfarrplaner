@foreach($participants as $participant)
    <span @if($vacation_check)@can('urlaub-lesen') @if (in_array($participant->lastName(), array_keys($vacations[$day->id]))) class="vacation-conflict"
          title="Konflikt mit Urlaub!" @endif @endcan @endif>@include('calendar.partials.name', ['person' => $participant])</span>
    @if($loop->last) @else | @endif
@endforeach
