<div class="card">
    @if(isset($cardHeader))
    <div class="card-header">
        @if(isset($collapseId))<a class="card-link" data-toggle="collapse" href="#{!! $collapseId !!}">@endif
        {!! $cardHeader !!}
        @if(isset($collapseId))</a>@endif
    </div>
    @endif
    @if(isset($collapseId))<div id="{{ $collapseId }}" class="collapse">@endif
    <div class="card-body">
        {!! $slot !!}
    </div>
    @if(isset($cardFooter))
    <div class="card-footer">
        {!! $cardFooter !!}
    </div>
    @endif
    @if(isset($collapseId))</div>@endif
</div>
