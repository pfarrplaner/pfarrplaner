@extends('mail.layout.default')

@section('preview')
    Änderungen am Gottesdienst vom {{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText() }} ({{ $service->locationText() }})
@endsection

@section('content')

    @component('mail.layout.blocks.content-table')
        @slot('title')
            Änderungen an einem Gottesdienst
        @endslot
        @slot('subtitle')
            {{ strftime('%A, %d. %B %Y', $service->day->date->timestamp) }}, {{ $service->timeText() }}
            <br>{{ $service->locationText() }}<br/>
            <small style="font-size: 8pt;">
                Geändert am {{ date('d.m.Y') }} um {{ date('H:i') }} Uhr
                durch {{ \Illuminate\Support\Facades\Auth::user()->name }}
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

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'Datum',
                     'key' => 'day_id',
                     'old' => $original->day->date->format('d.m.Y'),
                     'new' => $changed->day->date->format('d.m.Y')
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'Uhrzeit',
                     'key' => 'time',
                     'old' => $original->timeText(),
                     'new' => $changed->timeText()
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'Ort',
                     'key' => 'location_id',
                     'old' => $original->locationText(),
                     'new' => $changed->locationText(),
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'Freie Ortsangabe',
                     'key' => 'special_location',
                     'old' => $original->locationText(),
                     'new' => $changed->locationText(),
                     ])
            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'Beschreibung',
                     'key' => 'description',
                     'old' => trim($original->descriptionText()),
                     'new' => trim($changed->descriptionText()),
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'Interne Anmerkungen',
                     'key' => 'internal_remarks',
                     'old' => $original->internal_remarks,
                     'new' => $changed->internal_remarks,
                     ])

            @if(isset($original->city) && isset($changed->city))
                @include('mail.notifications.service.changed-attribute', [
                         'title' => 'Kirchengemeinde',
                         'key' => 'city_id',
                         'old' => $original->city->name,
                         'new' => $changed->city->name,
                         ])
            @endif

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'need_predicant',
                     'key' => 'Prädikant benötigt',
                     'old' => $original->need_predicant ? '✔' : '✘',
                     'new' => $changed->need_predicant ? '✔' : '✘',
                     ])

            @include('mail.notifications.service.changed-participants-list', [
                     'title' => 'Pfarrer*in',
                     'key' => 'pastors',
                     ])

            @include('mail.notifications.service.changed-participants-list', [
                     'title' => 'Organist*in',
                     'key' => 'organists',
                     ])

            @include('mail.notifications.service.changed-participants-list', [
                     'title' => 'Mesner*in',
                     'key' => 'sacristans',
                     ])

            @include('mail.notifications.service.changed-ministries')

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'baptism',
                     'key' => 'Taufgottesdienst',
                     'old' => $original->baptism ? '✔' : '✘',
                     'new' => $changed->baptism ? '✔' : '✘',
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'eucharist',
                     'key' => 'Abendmahlsgottesdienst',
                     'old' => $original->eucharist ? '✔' : '✘',
                     'new' => $changed->eucharist ? '✔' : '✘',
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'offerings_counter1',
                     'key' => 'Opferzähler 1',
                     'old' => $original->offerings_counter1,
                     'new' => $changed->offerings_counter1,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'offerings_counter2',
                     'key' => 'Opferzähler 2',
                     'old' => $original->offerings_counter2,
                     'new' => $changed->offerings_counter2,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'offering_goal',
                     'key' => 'Opferzweck',
                     'old' => $original->offering_goal,
                     'new' => $changed->offering_goal,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'offering_description',
                     'key' => 'Beschreibung zum Opfer',
                     'old' => $original->offering_description,
                     'new' => $changed->offering_description,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'offering_type',
                     'key' => 'Opfertyp',
                     'old' => $original->offering_type,
                     'new' => $changed->offering_type,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'offering_amount',
                     'key' => 'Opfersumme',
                     'old' => $original->offering_amount,
                     'new' => $changed->offering_amount,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'cc',
                     'key' => 'Kinderkirche',
                     'old' => $original->cc ? '✔' : '✘',
                     'new' => $changed->cc ? '✔' : '✘',
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'cc_alt_time',
                     'key' => 'Uhrzeit der Kinderkirche',
                     'old' => $original->cc_alt_time,
                     'new' => $changed->cc_alt_time,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'cc_location',
                     'key' => 'Ort der Kinderkirche',
                     'old' => $original->cc_location,
                     'new' => $changed->cc_location,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'cc_lesson',
                     'key' => 'Lektion für die Kinderkirche',
                     'old' => $original->cc_lesson,
                     'new' => $changed->cc_lesson,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'cc_staff',
                     'key' => 'Mitarbeiter in der Kinderkirche',
                     'old' => $original->cc_staff,
                     'new' => $changed->cc_staff,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'youtube_url',
                     'key' => 'Streaming-URL (youtube)',
                     'old' => $original->youtube_url,
                     'new' => $changed->youtube_url,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'cc_streaming_url',
                     'key' => 'URL für das Streaming der Kinderkirche',
                     'old' => $original->cc_streaming_url,
                     'new' => $changed->cc_streaming_url,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'offerings_url',
                     'key' => 'URL für Onlinespenden',
                     'old' => $original->offerings_url,
                     'new' => $changed->offerings_url,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'meeting_url',
                     'key' => 'URL für ein "virtuelles Kirchencafé"',
                     'old' => $original->meeting_url,
                     'new' => $changed->meeting_url,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'recording_url',
                     'key' => 'URL zur Audioaufzeichnung',
                     'old' => $original->recording_url,
                     'new' => $changed->recording_url,
                     ])

            @include('mail.notifications.service.changed-attribute', [
                     'title' => 'songsheet',
                     'key' => 'URL zum Liedblatt',
                     'old' => $original->songsheet,
                     'new' => $changed->songsheet,
                     ])

        @endcomponent


    @endcomponent

    @include('mail.layout.blocks.spacer')

    @include('mail.notifications.service.overview')


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
