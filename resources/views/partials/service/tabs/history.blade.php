<div role="tabpanel" id="history" class="tab-pane fade @if($tab == 'history') in active show @endif ">
    @if (count($service->revisionHistory))
        <b>Bisherige Änderungen an diesem Gottesdiensteintrag:</b><br />
        @foreach($service->revisionHistory as $history)
            @if($history->key == 'created_at' && !$history->old_value)
                {{ $history->created_at->format('d.m.Y, H:i:s') }}
                : {{ $history->userResponsible()->name }} hat diesen Eintrag
                angelegt: {{ $history->newValue() }}<br/>
            @else
                {{ $history->created_at->format('d.m.Y, H:i:s') }}
                : {{ $history->userResponsible()->name }} hat "{{ $history->fieldName() }}" von
                "{{ $history->oldValue() }}" zu "{{ $history->newValue() }}" geändert.
                <br/>
            @endif
        @endforeach
    @endif
</div>
