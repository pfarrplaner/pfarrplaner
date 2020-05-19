@if(isset($changes[$key]))
    <tr>
        @component('mail.layout.blocks.cell'){{ $title }} @endcomponent
        @component('mail.layout.blocks.cell')
            @foreach($originalRelations[$key] as $person)
                    <span  style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                    {{ $person->fullName(true) }}
                </span><br />
            @endforeach
        @endcomponent
        @component('mail.layout.blocks.cell')
            @foreach($changed->$key as $person)
                <span  style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                    {{ $person->fullName(true) }}
                </span><br />
            @endforeach
        @endcomponent
    </tr>
@endif
