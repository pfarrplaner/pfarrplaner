@foreach($items as $item)
    <span class="badge badge-{{ $badge_type ?? 'primary' }}">{{ $item }}</span>
@endforeach
