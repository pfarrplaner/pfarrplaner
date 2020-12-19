{!! $service->titleText(false) !!} am {!! $service->day->date->format('d.m.Y') !!} @if(isset($liturgy['title'])) ({!! $liturgy['title'] !!})@endif mit {!! $service->participantsText('P') !!}@if ($service->descriptionText()!='')


{!! $service->descriptionText() !!}@endif @if($service->songsheet)


Sing mit! Ein Liedblatt zu diesem Gottesdienst gibt es hier zum Download:
{!! $service->songsheetUrl !!}@endif @if($service->offering_goal)


Zu diesem Gottesdienst bitten wir um Spenden für folgenden Zweck: {!! $service->offering_goal !!}@if($service->offerings_url)
Spenden kannst du ganz einfach online hier: {!! $service->offerings_url !!} @endif @endif @if($service->sermon_title)


Predigt: "{!! $service->sermon_title !!}" ({!! $service->sermon_reference !!})

{!! $service->sermon_description !!}@if($service->external_url)


Zu dieser Predigt gibt es noch mehr Infos auf folgender Seite:
{!! $service->external_url !!}@endif @endif @if($service->cc_streaming_url)


Natürlich gibt es online auch einen Kindergottesdienst:
{!! $service->cc_streaming_url !!} @endif
