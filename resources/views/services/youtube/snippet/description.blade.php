{!! $service->titleText(false) !!} am {!! $service->day->date->format('d.m.Y') !!} @if(isset($liturgy['title'])) ({!! $liturgy['title'] !!})@endif mit {!! $service->participantsText('P') !!}@if($service->youtube_prefix_description)


{!! $service->youtube_prefix_description !!} @endif    @if ($service->descriptionText(['needs_reservations'])!='')


{!! $service->descriptionText(['needs_reservations']) !!}@endif @if($service->songsheet)


Sing mit! Ein Liedblatt zu diesem Gottesdienst gibt es hier zum Download:
{!! $service->songsheetUrl !!}@endif @if($service->offering_goal)


Zu diesem Gottesdienst bitten wir um Spenden für folgenden Zweck: {!! $service->offering_goal !!}@if($service->offerings_url)

Spenden kannst du ganz einfach online hier:
{!! $service->offerings_url !!} @endif @endif @if($service->sermon)


Predigt: "{!! $service->sermon->fullTitle !!}" ({!! $service->sermon->reference !!})

{!! $service->sermon->summary !!}@if($service->external_url)


Zu dieser Predigt gibt es noch mehr Infos auf folgender Seite:
{!! $service->external_url !!}@endif @endif @if($service->cc_streaming_url)


Natürlich gibt es online auch einen Kindergottesdienst:
{!! $service->cc_streaming_url !!} @endif @if($service->meeting_url)


Direkt im Anschluss an den Gottesdienst laden wir online zu einem "virtuellen Kirchencafé" ein. Wenn du teilnehmen möchtest,
klicke einfach auf den folgenden Link:
{!! $service->meeting_url !!} @endif @if($service->youtube_postfix_description)


{!! $service->youtube_postfix_description !!} @endif
