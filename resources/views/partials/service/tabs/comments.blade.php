<div role="tabpanel" class="tab-pane fade @if($tab == 'comments') in active show @endif " id="comments">

    @include('partials.comments.list', ['owner' => $service, 'ownerClass' => 'App\\Service'])
</div>