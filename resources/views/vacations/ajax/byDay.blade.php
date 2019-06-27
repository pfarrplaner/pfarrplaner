@if (count($vacations))
    @foreach ($vacations as $vacation) <div class="vacation">{{ $vacation }}</div>
    @endforeach
@endif
