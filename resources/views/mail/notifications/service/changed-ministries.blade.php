@if(is_array($original['attributes']['ministriesByCategory']) && is_array($changes['attributes']['ministriesByCategory']) && is_array($original['attributes']['ministriesByCategory']) && is_array($changes['attributes']['ministriesByCategory']))
    @foreach(array_unique(array_merge(array_keys($changes['attributes']['ministriesByCategory']['original']), array_keys($changes['attributes']['ministriesByCategory']['changed']))) as $ministry)
        @if(($changes['attributes']['ministriesByCategory']['original'][$ministry] ?? collect([]))->pluck('id')->join(',') != ($changes['attributes']['ministriesByCategory']['changed'][$ministry] ?? collect([]))->pluck('id')->join(','))
            <tr>
                @component('mail.layout.blocks.cell'){{ $ministry }} @endcomponent
                @component('mail.layout.blocks.cell')
                    @if(is_array($changes['attributes']['ministriesByCategory']['original'][$ministry]))
                        @foreach($changes['attributes']['ministriesByCategory']['original'][$ministry] as $person)
                            <span
                                style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                            {{ $person->fullName(true) }}
                        </span><br/>
                        @endforeach
                    @endif
                @endcomponent
                @component('mail.layout.blocks.cell')
                    @if(is_array($changes['attributes']['ministriesByCategory']['changed'][$ministry]))
                        @foreach($changes['attributes']['ministriesByCategory']['changed'][$ministry] as $person)
                            <span
                                style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                        {{ $person->fullName(true) }}
                    </span><br/>
                        @endforeach
                    @endif
                @endcomponent
            </tr>
        @endif
    @endforeach
@endif
