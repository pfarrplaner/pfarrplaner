@if($nameFormat==\App\User::NAME_FORMAT_DEFAULT){{ $participant->lastName(true) }}@endif
@if($nameFormat==\App\User::NAME_FORMAT_INITIAL_AND_LAST){{ $participant->initialedName(true) }}@endif
@if($nameFormat==\App\User::NAME_FORMAT_FIRST_AND_LAST){{ $participant->fullName(true) }}@endif

