<!DOCTYPE html>
<html lang="de">
<head>
    <style>
        @font-face {
            font-face-name: 'Helvetica Condensed';
        }

        body {
            line-height: 1.1em;
            font-size: 180pt;
            font-family: 'Helvetica Condensed', sans-serif;
        }

        img {
            max-height: .8em;
            height: .8em;
            padding: 0;
            margin: 0;
        }

    </style>
</head>
<body>
@foreach($service->liturgyBlocks as $block)
    @foreach ($block->items as $item)
        @if($item->data_type == 'song')
            @if(isset($item->data['song']) && isset($item->data['song']['song']))
                @if (isset($item->data['song']['code']) || isset($item->data['song']['songbook']))@if($item->data['song']['code']!='EG')
                    @if(isset($item->data['song']['songbook']['image']))
                        <img src="{{ asset(str_replace('attachments/', 'image/', $item->data['song']['songbook']['image'])) }}" />
                    @else
                        {{ $item->data['song']['code'] ?: ($item['song']['songbook']['name'] ?: '') }}
                    @endif
                @endif @endif
                {{ $item->data['song']['reference'] ?: '' }}@if ($item->data['verses']), {{ $item->data['verses'] }}@endif
                <br/>
            @endif
        @elseif($item->data_type == 'psalm')
            @if(isset($item->data['psalm']))
                @if (isset($item->data['song']['code']) || isset($item->data['song']['songbook']))@if($item->data['song']['code']!='EG')
                    @if(isset($item->data['song']['songbook']['image']))
                        <img src="{{ asset(str_replace('attachments/', 'image/', $item->data['song']['songbook']['image'])) }}" />
                    @else
                        {{ $item->data['song']['code'] ?: ($item['song']['songbook']['name'] ?: '') }}
                    @endif
                @endif @endif
                {{ $item->data['psalm']['reference'] ?: '' }}
                {{ substr($item->data['psalm']['title'], 0, 5) == 'Psalm' ? 'Ps. '.trim(substr($item->data['psalm']['title'], 6)) : '' }}
                <br/>
            @endif
        @endif
    @endforeach
@endforeach
</body>
</html>
