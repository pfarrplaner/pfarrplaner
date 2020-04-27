<?xml version="1.0" encoding="utf-8"?>
<rss xmlns:atom="http://www.w3.org/2005/Atom" xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:content="http://purl.org/rss/1.0/modules/content/" version="2.0">
    <channel>
        <title>{{ $city->podcast_title }}</title>
        <description>Predigten aus {{ $city->name }}</description>
        <link>{{ $city->homepage }}</link>
        <lastBuildDate>{{ Carbon\Carbon::now()->format('D, d M Y H:i:s O') }}</lastBuildDate>
        <pubDate>{{ Carbon\Carbon::now()->format('D, d M Y H:i:s O') }}</pubDate>e
        <generator>Pfarrplaner</generator>
        <image>
            <url>{{ asset($city->podcast_logo) }}</url>
            <title>{{ $city->podcast_title }}</title>
            <link>{{ $city->homepage }}</link>
            <description>Predigten aus {{ $city->name }}</description>
        </image>
	<itunes:image href="{{ asset($city->podcast_logo) }}" />
        <language>de</language>
        <copyright>(c) {{ $city->podcast_owner_name }}</copyright>
        <category>Religion &amp; Spirituality / Christianity</category>

        <itunes:owner>
            <itunes:name>{{ $city->podcast_owner_name }}</itunes:name>
            <itunes:email>{{ $city->podcast_owner_email }}</itunes:email>
        </itunes:owner>
        <itunes:author>{{ $city->podcast_owner_name }}</itunes:author>
        <itunes:summary>Predigten aus {{ $city->name }}</itunes:summary>
        <itunes:category text="Religion &amp; Spirituality">
            <itunes:category text="Christianity" />
        </itunes:category>
        <itunes:explicit>no</itunes:explicit>
        <atom:link href="{{ strtolower(route('podcast', $city->name)) }}" rel="self" type="application/rss+xml" />

        @foreach($services as $service)
        <item>
            <title>@if($service->sermon_title) {{ $service->sermon_title }} @else Predigt vom {{ $service->day->date->format('d.m.Y') }}, @if($service->participantsText('Predigt')){{ $service->participantsText('Predigt') }} @else {{ $service->participantsText('P') }} @endif @endif</title>
            <itunes:image href="{{ asset($service->sermon_image ?: $service->city->sermon_default_image) }}" />
            <itunes:summary><![CDATA[ @if($service->sermon_description){{ $service->sermon_description }} @else Predigt vom {{ $service->day->date->format('d.m.Y') }}, @if($service->participantsText('Predigt')){{ $service->participantsText('Predigt', true) }} @else {{ $service->participantsText('P', true) }} @endif @endif ]]></itunes:summary>
            <link>{{ $city->homepage.(substr($city->homepage, -1) == '/' ? '' : '/') }}?podcast_id={{ md5( $service->id) }}</link>
            <guid>{{ $city->homepage.(substr($city->homepage, -1) == '/' ? '' : '/') }}?podcast_id={{ md5( $service->id) }}</guid>
            <description><![CDATA[ @if($service->sermon_description){{ $service->sermon_description }} @else Predigt vom {{ $service->day->date->format('d.m.Y') }}, @if($service->participantsText('Predigt')){{ $service->participantsText('Predigt', true) }} @else {{ $service->participantsText('P', true) }} @endif @endif ]]></description>
            <content:encoded><![CDATA[ @if($service->sermon_description){{ $service->sermon_description }} @else Predigt vom {{ $service->day->date->format('d.m.Y') }}, @if($service->participantsText('Predigt')){{ $service->participantsText('Predigt', true) }} @else {{ $service->participantsText('P', true) }} @endif @endif ]]></content:encoded>
            <enclosure url="{{ $service->recording_url }}" length="{{ \App\Helpers\PodcastHelper::getFileSize($service->recording_url) }}" type="audio/mpeg"></enclosure>
            <pubDate>{{ $service->day->date->format('D, d M Y H:i:s O') }}</pubDate>
        </item>
        @endforeach


    </channel>
</rss>
