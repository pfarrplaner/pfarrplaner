<html>
<head>
    <style>
        @page {
            margin-top: 9mm;
            margin-bottom: 9mm;
            margin-left: 9mm;
            margin-right: 9mm;
        }

        body, * {
            font-family: 'helveticacondensed', sans-serif;
        }

        h1 {
            font-family: 'helveticacondensed', sans-serif;
            font-size: 18px;
        }

        .label {
            width: 28mm;
            font-size: 11pt;
            font-weight: bold;
            float: left;
        }

        .input {
            background-color: lightgray;
            border: none;
            height: 6mm;
            float: left;
            font-size: 11pt;
            width: 46mm;
            border-right: solid 2mm white;
            padding: 1mm;
        }

        .input-area {
            float: left;
        }

        .input-large {
            width: 96mm;
        }

        .input-label {
            font-size: 7pt;
            width: 48mm;
            border-right: solid 2mm white;
            float: left;
        }

        .clear {
            clear: both;
        }

        .check {
            width: 48mm;
            float: left;
            border-right: solid 2mm white;
            font-size: 11px;
        }

    </style>
</head>
<body>
<h1>Evangelisches Kirchenregisteramt {{ $funeral->service->city->name }}</h1>
<form>
    <div class="row">
        <div class="label">Verstorbene/r</div>
        <div class="input-area">
            <div class="input input-large">
                {{ $funeral->buried_name }}
            </div>
            <div class="input-label">Name</div>
            <div class="clear"></div>
            <div class="input">
                {{ $funeral->buried_address }}
            </div>
            <div class="input">
                {{ $funeral->buried_zip }} {{ $funeral->buried_city }}
            </div>
            <div class="input-label">Straße</div>
            <div class="input-label">Wohnort</div>

        </div>
    </div>
    <div style="width: 100%; height: 9mm;"></div>
    <div class="row">
        <div class="label">Bestattung</div>
        <div class="input-area">
            <div class="input">
                {{ $funeral->service->date->format('d.m.Y') }}
            </div>
            <div class="input">
                @if(is_object($funeral->service->location)) {{ $funeral->service->location->city->name }} @endif
            </div>
            <div class="input-label">Datum</div>
            <div class="input-label">Ort</div>
            <div class="clear"></div>
            <div class="input">
                {{ $funeral->service->locationText() }}
            </div>
            <div class="input">
                {{ $funeral->service->timeText() }}
            </div>
            <div class="input-label">Friedhof</div>
            <div class="input-label">Zeit</div>
            <div class="clear"></div>
            <div class="input">
                {{ $funeral->service->participantsText('P', true) }}
            </div>
            <div class="input">
                {{ $funeral->service->participantsText('O', true) }}
            </div>
            <div class="input-label">Pfarrer:in</div>
            <div class="input-label">Organist:in</div>
            <div class="clear"></div>
            <div class="input">
                {{ $funeral->text }}
            </div>
            <div class="input">
                @if (!is_null($funeral->announcement)){{ $funeral->announcement->format('d.m.Y') }}@endif
            </div>
            <div class="input-label">Bestattungstext</div>
            <div class="input-label">Abkündigung am</div>
            <div style="height: 5mm;"></div>
            <div style="vertical-align: bottom">
                <div class="check">
                    <input type="checkbox" name="type" @if($funeral->type=='Erdbestattung')checked="checked" @endif>
                    Erdbestattung <br/>
                    <input type="checkbox" name="type" @if($funeral->type=='Trauerfeier')checked="checked" @endif>
                    Trauerfeier <br/>
                    <input type="checkbox" name="type"
                           @if($funeral->type=='Trauerfeier mit Urnenbeisetzung')checked="checked" @endif> Trauerfeier
                    mit Urnenbeisetzung<br/>
                    <input type="checkbox" name="type" @if($funeral->type=='Urnenbeisetzung')checked="checked" @endif>
                    Urnenbeisetzung
                </div>
                <div style="float: left;">
                    <div class="input">
                        @if($funeral->type=='Urnenbeisetzung') {{ $funeral->wake_location }} @endif
                    </div>
                    <div class="input">
                        @if($funeral->type=='Urnenbeisetzung' && (!is_null($funeral->wake))) {{ $funeral->wake->format('d.m.Y') }} @endif
                    </div>
                    <div class="input-label">Ort und Datum der vorausgegangenen Trauerfeier</div>
                </div>
            </div>
        </div>
    </div>
    <div style="width: 100%; height: 9mm;"></div>
    <div class="row">
        <div class="label">Angehörige/r</div>
        <div class="input-area">
            <div class="input input-large">
                {{ $funeral->relative_name }}
            </div>
            <div class="input-label">Name</div>
            <div class="clear"></div>
            <div class="input">
                {{ $funeral->relative_address }}
            </div>
            <div class="input">
                {{ $funeral->relative_zip }} {{ $funeral->relative_city }}
            </div>
            <div class="input-label">Straße</div>
            <div class="input-label">Wohnort</div>

        </div>
    </div>
    <div style="width: 100%; height: 9mm;"></div>
    <div class="row">
        <div class="label">Kirchenregister</div>
        <div class="input-area">
            <div class="input input-large">
            </div>
            <div class="input-label">Familienverzeichnis</div>
            <div class="clear"></div>
            <div class="input input-large">
            </div>
            <div class="input-label">Taufverzeichnis</div>
            <div class="clear"></div>
            <div class="input input-large">
            </div>
            <div class="input-label">Begräbnisregister</div>
            <div class="clear"></div>
        </div>
    </div>

</form>
</body>
</html>
