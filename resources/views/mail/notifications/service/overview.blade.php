@component('mail.layout.blocks.content-table')
    @slot('title')
        Der komplette Gottesdienst in der Übersicht
    @endslot
    @slot('subtitle')
        {{ strftime('%A, %d. %B %Y', $service->day->date->timestamp) }}, {{ $service->timeText() }}<br>{{ $service->locationText() }}<br />
    @endslot

    @component('mail.layout.blocks.section')
        @slot('title')
        @endslot
        @slot('subtitle')
        @endslot
        @slot('header')
            @component('mail.layout.blocks.cell', ['type' => 'th']) Datenfeld @endcomponent
            @component('mail.layout.blocks.cell', ['type' => 'th']) Inhalt @endcomponent
        @endslot
        <tr>
            @component('mail.layout.blocks.cell')Datum @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->day->date->format('d.m.Y') }}@endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Uhrzeit @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->timeText() }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Kirchengemeinde @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->city->name }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Ort @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->locationText() }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Beschreibung @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->descriptionText() }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Interne Notizen @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->internal_remarks }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Taufgottesdienst @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->baptism ? '✔' : '✘'}} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Abendmahl @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->eucharist ? '✔' : '✘'}} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Prädikant benötigt @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->need_predicant ? '✔' : '✘'}} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Pfarrer*in @endcomponent
            @component('mail.layout.blocks.cell')
                @foreach($service->pastors as $person)
                    <span  style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                        {{ $person->fullName(true) }}
                    </span><br />
                @endforeach
            @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Organist*in @endcomponent
            @component('mail.layout.blocks.cell')
                @foreach($service->organists as $person)
                    <span  style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                        {{ $person->fullName(true) }}
                    </span><br />
                @endforeach
            @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Mesner*in @endcomponent
            @component('mail.layout.blocks.cell')
                @foreach($service->sacristans as $person)
                    <span  style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                        {{ $person->fullName(true) }}
                    </span><br />
                @endforeach
            @endcomponent
        </tr>
        @foreach($service->ministriesByCategory as $ministry => $people)
            <tr>
                @component('mail.layout.blocks.cell'){{ $ministry }} @endcomponent
                @component('mail.layout.blocks.cell')
                    @foreach($people as $person)
                        <span  style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                        {{ $person->fullName(true) }}
                    </span><br />
                    @endforeach
                @endcomponent
            </tr>
        @endforeach
        <tr>
            @component('mail.layout.blocks.cell')Weitere Beteiligte @endcomponent
            @component('mail.layout.blocks.cell')
                @foreach($service->otherParticipants as $person)
                    <span  style=" @if($user->id == $person->id) background-color: lightgreen; @endif padding: 2px; border-radius: 3px;">
                        {{ $person->fullName(true) }}
                    </span><br />
                @endforeach
            @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Opferzähler 1 @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->offerings_counter1 }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Opferzähler 2 @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->offerings_counter2 }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Opferzweck @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->offering_goal }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Notizen zum Opfer @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->offering_description }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Opfertyp @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->offering_type }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Opfersumme @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->offering_amount }} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Kinderkirche @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->cc ? '✔' : '✘'}} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Uhrzeit der Kinderkirche @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->cc_alt_time ? '✔' : '✘'}} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Ort der Kinderkirche @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->cc_location ? '✔' : '✘'}} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Thema der Kinderkirche @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->cc_lesson ? '✔' : '✘'}} @endcomponent
        </tr>
        <tr>
            @component('mail.layout.blocks.cell')Mitarbeiter der Kinderkirche @endcomponent
            @component('mail.layout.blocks.cell'){{ $service->cc_staff ? '✔' : '✘'}} @endcomponent
        </tr>

    @endcomponent


@endcomponent

@include('mail.layout.blocks.spacer')
