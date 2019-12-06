<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#" title="Benachrichtigungen">
        <i class="far fa-bell"></i>
        @if($c = \App\UI\FlashMessages::count())<span class="badge badge-info navbar-badge">{{ $c }}</span>@endif
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-item dropdown-header">{{ $c }} Benachrichtigungen</span>
        <div class="dropdown-divider"></div>
        @foreach(\App\UI\FlashMessages::all() as $message)
            <a href="#" class="dropdown-item">
                <i class="fas fa-envelope mr-2"></i> {{ $message['text'] }}
                <span class="float-right text-muted text-sm"><i class="fa fa-close"></i></span>
            </a>
        @endforeach
    </div>
</li>
