<tr>
    @component('mail.layout.blocks.cell'){{ $category }} @endcomponent
    @foreach (['original', 'changed'] as $type)
        @component('mail.layout.blocks.cell')
            @if(isset($ministry[$type]))
                @foreach($ministry[$type] as $person)
                    <?php $person = \App\User::find($person['id']); ?>
                    <span
                        style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                                    {{ $person->fullName(true) }}
                                </span><br/>
                @endforeach
            @endif
        @endcomponent
    @endforeach
</tr>
