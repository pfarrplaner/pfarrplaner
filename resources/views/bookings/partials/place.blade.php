@if(!isset($seating['grid'][$place])){{ $place }}
@else
    <span style="display: inline-block;
        @if(is_numeric($place) && ($seating['grid'][$place]->seats > 1))
        padding: 1px 30px 1px 30px;
        @else padding: 1px 3px; @endif
        border: solid 1px darkgray; border-radius: 3px;
        box-shadow: darkgray 0px 0px 1px 1px;
        margin-right: 3px;
        margin-bottom: 3px;
        background-color: {{ $seating['grid'][$place]->color ?: $seating['grid'][$place]->seatingSection->color ?: 'white' }};
        @if(isset($taken) && $taken) @if($auto) font-style: italic;  @else font-weight: bold; box-shadow: red 0px 0px 1px 1px; @endif @endif
        "
    title="max. {{ $seating['grid'][$place]->seats.($seating['grid'][$place]->seats > 1 ? ' Personen':' Person').(isset($taken) ? ($auto ? ' (vorläufig zugewiesen)' : ' (manuell zugewiesen)') : '') }}">
        {{ $place }} @if(is_numeric($place) && ($seating['grid'][$place]->seats > 1))<span class="fa fa-couch"></span> @else @for($i=1;$i<=$seating['grid'][$place]->seats;$i++)<span class="fa fa-chair" @if(isset($number) && ($i>$number)) style="color: gray;"@endif></span>@endfor @endif
        </span>
@endif
