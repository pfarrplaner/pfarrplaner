<li class="nav-item">
    <a class="nav-link @if(isset($active) && $active) active @endif" href="#{{ $id }}" role="tab" data-toggle="tab">{{ $title }}@if(isset($count) && ($count > 0)) <span
            class="badge badge-{{ $badge_type ?? 'primary' }}">{{ $count }}</span> @endif</a>
</li>
