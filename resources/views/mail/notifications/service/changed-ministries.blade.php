@if(isset($changes['ministriesByCategory']))
    @if(is_array($original->originalAppendedAttributes['ministriesByCategory']))
        @foreach(array_unique(array_merge(array_keys($service->ministriesByCategory), array_keys($original->originalAppendedAttributes['ministriesByCategory']))) as $ministry)
            <tr>
                @component('mail.layout.blocks.cell'){{ $ministry }} @endcomponent
                @component('mail.layout.blocks.cell')
                    @foreach(($original->originalAppendedAttributes['ministriesByCategory'][$ministry] ?? []) as $person)
                        <?php $person = \App\User::find($person['id']); ?>
                        <span
                            style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                        {{ $person->fullName(true) }}
                    </span><br/>
                    @endforeach
                @endcomponent
                @component('mail.layout.blocks.cell')
                    @foreach(($service->ministriesByCategory[$ministry] ?? []) as $person)
                        <span
                            style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                        {{ $person->fullName(true) }}
                    </span><br/>
                    @endforeach
                @endcomponent
            </tr>
        @endforeach
    @endif
@endif
