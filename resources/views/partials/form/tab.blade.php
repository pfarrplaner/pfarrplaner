<div role="tabpanel" class="tab-pane fade @if(isset($active) && $active) in active show @endif" id="{{ $id }}">
    {{ $slot }}
</div>