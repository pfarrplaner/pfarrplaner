@if(isset($changes['relations'][$key]))
    <tr>
        @component('mail.layout.blocks.cell'){{ $title }} @endcomponent
        @component('mail.layout.blocks.cell')
            @foreach($changes['relations'][$key]['original'] as $person)
                    <span  style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                    {{ $person->fullName(true) }}
                </span><br />
            @endforeach
        @endcomponent
        @component('mail.layout.blocks.cell')
            @foreach($changes['relations'][$key]['changed'] as $person)
                <span  style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                    {{ $person->fullName(true) }}
                </span><br />
            @endforeach
        @endcomponent
    </tr>
@endif
