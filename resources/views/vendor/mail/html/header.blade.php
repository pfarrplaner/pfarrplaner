<tr>
<td class="header">
@if(isset($url))<a href="{{ $url ?? ''}}">
{{ $slot }}
</a>@else {{ $slot }} @endif
</td>
</tr>
