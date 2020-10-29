@extends('mail.layout.default')

@section('preview')
    Änderungen am Gottesdienst vom {{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText() }} ({{ $service->locationText() }})
@endsection

@section('content')

    @component('mail.layout.blocks.content-table')
        @slot('title')
            Änderungen am Gottesdienst vom {{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText() }} ({{ $service->locationText() }})
        @endslot
        @slot('subtitle')
            {{ strftime('%A, %d. %B %Y', $service->day->date->timestamp) }}, {{ $service->timeText() }}
            <br>{{ $service->locationText() }}<br/>
            <small style="font-size: 8pt;">
                Geändert am {{ date('d.m.Y') }} um {{ date('H:i') }} Uhr
                durch {{ $originatingUser->name }}
            </small>
        @endslot

        @component('mail.layout.blocks.section')
            @slot('title')
            @endslot
            @slot('subtitle')
            @endslot
            @slot('header')
                @component('mail.layout.blocks.cell', ['type' => 'th']) Datenfeld @endcomponent
                @component('mail.layout.blocks.cell', ['type' => 'th']) Vorher @endcomponent
                @component('mail.layout.blocks.cell', ['type' => 'th']) Nachher @endcomponent
            @endslot

            @foreach ([
                'dateText' => 'Datum',
                'timeText' => 'Uhrzeit',
                'locationText' => 'Ort',
                'descriptionText' => 'Beschreibung',
                'internal_remarks' => 'Interne Anmerkungen',
                ] as $attribute => $title)
                @if(isset($changes[$attribute]))
                    @include('mail.notifications.service.changed-attribute', [
                             'title' => $title,
                             'old' => $changes[$attribute]['original'],
                             'new' => $changes[$attribute]['changed'],
                             ])
                @endif
            @endforeach

            @if(isset($changes['hidden']))
                @include('mail.notifications.service.changed-attribute', [
                         'title' => 'hidden',
                         'key' => 'In öffentlichen Listen verbergen',
                         'old' => $changes['hidden']['original'] ? '✔' : '✘',
                         'new' => $changes['hidden']['changed'] ? '✔' : '✘',
                         ])
            @endif

            @if(isset($changes['city_id']))
                @include('mail.notifications.service.changed-attribute', [
                         'title' => 'Kirchengemeinde',
                         'key' => 'city_id',
                         'old' => \App\City::find($changes['city_id']['original'])->name,
                         'new' => $service->city->name,
                         ])
            @endif

            @if(isset($changes['need_predicant']))
            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'need_predicant',
                     'key' => 'Prädikant benötigt',
                     'old' => $original->need_predicant ? '✔' : '✘',
                     'new' => $changed->need_predicant ? '✔' : '✘',
                     ])
            @endif

            @foreach ($participants as $category => $categoryParticipants)
                @include('mail.notifications.service.changed-ministries', [
                    'category' => $category,
                    'ministry' => $categoryParticipants,
                ])
            @endforeach

            @if(isset($changes['baptism']))
            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'baptism',
                     'key' => 'Taufgottesdienst',
                     'old' => $changes['baptism']['original'] ? '✔' : '✘',
                     'new' => $changes['baptism']['changed'] ? '✔' : '✘',
                     ])
            @endif

            @if(isset($changes['eucharist']))
            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'eucharist',
                     'key' => 'Abendmahlsgottesdienst',
                     'old' => $changes['eucharist']['original'] ? '✔' : '✘',
                     'new' => $changes['eucharist']['changed'] ? '✔' : '✘',
                     ])
            @endif


            @foreach ([
                'offerings_counter1' => 'Opferzähler 1',
                'offerings_counter2' => 'Opferzähler 2',
                'offering_goal' => 'Opferzweck',
                'offering_description' => 'Beschreibung zum Opfer',
                'offering_type' => 'Opfertyp',
                'offering_type' => 'Opfertyp',
                'offering_amount' => 'Opfersumme',
                ] as $attribute => $title)
                @if(isset($changes[$attribute]))
                    @include('mail.notifications.service.changed-attribute', [
                             'title' => $title,
                             'old' => $changes[$attribute]['original'],
                             'new' => $changes[$attribute]['changed'],
                             ])
                @endif
            @endforeach

            @if(isset($changes['cc']))
            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'cc',
                     'key' => 'Kinderkirche',
                     'old' => $changes['cc']['original'] ? '✔' : '✘',
                     'new' => $changes['cc']['changed'] ? '✔' : '✘',
                     ])
            @endif

            @foreach ([
                'cc_alt_time' => 'Uhrzeit der Kinderkirche',
                'cc_location' => 'Ort der Kinderkirche',
                'cc_lession' => 'Lektion für die Kinderkirche',
                'cc_staff' => 'Mitarbeiter in der Kinderkirche',
                'youtube_url' => 'Streaming-URL (youtube)',
                'cc_streaming_url' => 'URL für das Streaming der Kinderkirche',
                'offerings_url' => 'URL für Onlinespenden',
                'meeting_url' => 'URL für ein "virtuelles Kirchencafé"',
                'recording_url' => 'URL zur Audioaufzeichnung',
                'songsheet' => 'URL zum Liedblatt',
                'external_url' => 'Externe Seite zum Gottesdienst',
                'sermon_title' => 'Titel der Predigt',
                'sermon_reference' => 'Predigttext',
                'sermon_image' => 'Titelbild der Predigt',
                'sermon_description' => 'Beschreibungstext zur Predigt',
                ] as $attribute => $title)
                @if(isset($changes[$attribute]))
                    @include('mail.notifications.service.changed-attribute', [
                             'title' => $title,
                             'old' => $changes[$attribute]['original'],
                             'new' => $changes[$attribute]['changed'],
                             ])
                @endif

                @if(isset($changes['needs_reservations']))
                    @include('mail.notifications.service.changed-attribute', [
                             'title' => 'needs_reservations',
                             'key' => 'Anmeldung benötigt',
                             'old' => $changes['needs_reservations']['original'] ? '✔' : '✘',
                             'new' => $changes['needs_reservations']['changed'] ? '✔' : '✘',
                             ])
                @endif

                @if(isset($changes['exclude_sections']))
                    @include('mail.notifications.service.changed-attribute', [
                             'title' => 'exclude_sections',
                             'key' => 'Sitzplätze in folgenden Zonen nicht belegen',
                             'old' => $changes['exclude_sections']['original'],
                             'new' => $changes['exclude_sections']['changed'],
                             ])
                @endif


            @endforeach
        @endcomponent


    @endcomponent

    @include('mail.layout.blocks.spacer')

    @if($user->can('gd-kasualien-lesen') || $user->can('gd-kasualien-bearbeiten'))
        @if($service->baptisms->count() || $service->funerals->count() || $service->weddings->count())

            @component('mail.layout.blocks.content-table')
                @slot('title')
                    Kasualien in diesem Gottesdienst
                @endslot
                @slot('subtitle')

                    @include('mail.notifications.service.baptisms')
                    @include('mail.notifications.service.weddings')
                    @include('mail.notifications.service.funerals')

                @endslot

            @endcomponent


        @endif

        @include('mail.layout.blocks.spacer')

    @endif

    @include('mail.notifications.service.comments')
    @include('mail.layout.blocks.spacer')

    <table class="card w-100 " border="0" cellpadding="0" cellspacing="0"
           style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: separate !important; border-radius: 4px; width: 100%; overflow: hidden; border: 1px solid #dee2e6;"
           bgcolor="#ffffff">
        <tbody>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; width: 100%; margin: 0;"
                align="left">
                <div>
                    <table class="card-body" border="0" cellpadding="0" cellspacing="0"
                           style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%;">
                        <tbody>
                        <tr>
                            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 20px;"
                                align="left">
                                <div>
                                    <h4 class="text-center"
                                        style="margin-top: 0; margin-bottom: 0; font-weight: 500; color: inherit; vertical-align: baseline; font-size: 24px; line-height: 28.8px;"
                                        align="center">Alle Gottesdienste in der Übersicht</h4>
                                    <p class="text-center" style="line-height: 24px; font-size: 16px; margin: 0;"
                                       align="center">Unser Online-Dienstplan hilft dir, komplett den Überblick zu
                                        behalten</p>
                                    <table class="s-2 w-100" border="0" cellpadding="0" cellspacing="0"
                                           style="width: 100%;">
                                        <tbody>
                                        <tr>
                                            <td height="8"
                                                style="border-spacing: 0px; border-collapse: collapse; line-height: 8px; font-size: 8px; width: 100%; height: 8px; margin: 0;"
                                                align="left">

                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <table class="btn btn-primary btn-lg mx-auto " align="center" border="0"
                                           cellpadding="0" cellspacing="0"
                                           style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: separate !important; border-radius: 4px; margin: 0 auto;">
                                        <tbody>
                                        <tr>
                                            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-radius: 4px; margin: 0;"
                                                align="center" bgcolor="#007bff">
                                                @if($user->can('update', $service))<a
                                                    href="{{ route('services.edit', $service) }}"
                                                    style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; text-decoration: none; border-radius: 4.8px; line-height: 30px; display: inline-block; font-weight: normal; white-space: nowrap; background-color: #a61380; color: #ffffff; padding: 8px 16px;">Diesen
                                                    Gottesdienst ansehen</a>
                                                @else <a href="{{ route('calendar') }}"
                                                         style="font-size: 20px; font-family: Helvetica, Arial, sans-serif; text-decoration: none; border-radius: 4.8px; line-height: 30px; display: inline-block; font-weight: normal; white-space: nowrap; background-color: #007bff; color: #ffffff; padding: 8px 16px; border: 1px solid #007bff;">Kalender
                                                    öffnen</a>@endif
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </td>
        </tr>
        </tbody>
    </table>

    @include('mail.layout.blocks.spacer')



    <div class="text-center text-muted" style="color: #636c72; font-size: 8pt; line-height: 9pt; margin-top: 10px;"
         align="center">
        @if($user->getSubscriptionType($service->city) == \App\Subscription::SUBSCRIBE_ALL)
            Du erhältst diese Nachricht, weil du über alle Änderungen an Gottesdiensten in {{ $service->city->name }}
            benachrichtigt werden willst.
        @elseif($user->getSubscriptionType($service->city) == \App\Subscription::SUBSCRIBE_OWN)
            Du erhältst diese Nachricht, weil du über Änderungen an Gottesdiensten in {{ $service->city->name }}, an
            denen du beteiligt bist, benachrichtigt werden willst.
        @endif
        Diese Einstellung kannst du selbst <a href="{{ route('user.profile') }}">in deinem Benutzerprofil bei
            Pfarrplaner</a> ändern.
    </div>
    @include('mail.layout.blocks.hr')

    <table class="table-unstyled text-muted " border="0" cellpadding="0" cellspacing="0"
           style="font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-spacing: 0px; border-collapse: collapse; width: 100%; max-width: 100%; color: #636c72;"
           bgcolor="transparent">
        <tbody>
        <tr>
            <td style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 0; border-bottom-width: 0; margin: 0;"
                align="left">© 2019 Pfarrplaner
            </td>
            <td class="text-right"
                style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 16px; border-top-width: 0; border-bottom-width: 0; margin: 0;"
                align="right">
                <a class="text-muted" href="{{ env('APP_URL') }}" style="color: #636c72;">Jetzt einloggen</a>
            </td>
        </tr>
        </tbody>
    </table>
    <table class="s-4 w-100" border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
        <tbody>
        <tr>
            <td height="24"
                style="border-spacing: 0px; border-collapse: collapse; line-height: 24px; font-size: 24px; width: 100%; height: 24px; margin: 0;"
                align="left">

            </td>
        </tr>
        </tbody>
    </table>




@endsection
