<?php
$m = \App\Misc\VersionInfo::getMessages()->sortByDesc('date')->where('date', '>=', (Auth::user()->getSetting('new_features', \Carbon\Carbon::now()->subYear(1), false) ?: \Carbon\Carbon::createFromDate(2019,05, 14)->setTime(0,0,0)));
?>
<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" title="Neue Features im Pfarrplaner">
        <i class="fa fa-laptop"></i>
        @if($c = count($m))<span class="badge badge-info navbar-badge">{{ $c }}</span>@endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <a href="{{ route('whatsnew') }}"><span class="dropdown-item dropdown-header">{{ $c }} neue Features</span></a>
        <div class="dropdown-divider"></div>
        @foreach($m as $message)
            @if($loop->index < 5)
                <a href="{{ route('whatsnew') }}" class="dropdown-item" title="{{ strip_tags($message['text']) }}">
                    <i class="fas fa-envelope mr-2"></i> {!!  \App\Tools\StringTool::trimToLen($message['text'], 20) !!}
                    <span class="float-right text-muted text-sm">{{ $message['date']->diffForHumans() }}</span>
                </a>
            @endif
        @endforeach
    </div>
</li>
