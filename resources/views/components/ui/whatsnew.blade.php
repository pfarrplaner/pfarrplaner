<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" title="Neue Features im Pfarrplaner">
        <i class="fa fa-laptop"></i>
        @if($c = count(\App\Misc\VersionInfo::getMessages()))<span class="badge badge-warning navbar-badge">{{ $c }}</span>@endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">{{ $c }} neue Features</span>
        <div class="dropdown-divider"></div>
        @foreach(\App\Misc\VersionInfo::getMessages() as $message)
            @if($loop->index < 5)
                <a href="#" class="dropdown-item" title="{{ strip_tags($message['text']) }}">
                    <i class="fas fa-envelope mr-2"></i> {!!  \App\Tools\StringTool::trimToLen($message['text'], 20) !!}
                    <span class="float-right text-muted text-sm">{{ $message['date']->diffForHumans() }}</span>
                </a>
            @endif
        @endforeach
    </div>
</li>
