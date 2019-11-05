<div role="tabpanel" class="tab-pane fade @if($tab == 'home' || $tab == '') in active show @endif " id="home">
    <div class="form-group">
        <label for="day_id">Datum</label>
        <select class="form-control" name="day_id" @cannot('gd-allgemein-bearbeiten') disabled @endcannot >
            @foreach($days as $thisDay)
                <option value="{{$thisDay->id}}"
                        @if ($service->day->id == $thisDay->id) selected @endif
                >{{$thisDay->date->format('d.m.Y')}}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="location_id">Kirche / Gottesdienstort</label>
        <select class="form-control" name="location_id" @cannot('gd-allgemein-bearbeiten') disabled @endcannot >
            @foreach($locations as $thisLocation)
                <option data-time="{{ strftime('%H:%M', strtotime($thisLocation->default_time)) }}"
                        value="{{$thisLocation->id}}"
                        @if (is_object($service->location))
                        @if ($service->location->id == $thisLocation->id) selected @endif
                        @endif
                >
                    {{$thisLocation->name}}
                </option>
            @endforeach
            <option value=""
                    @if (!is_object($service->location)) selected @endif
            >Freie Ortsangabe</option>
        </select>
    </div>
    <div id="special_location" class="form-group">
        <label for="special_location">Freie Ortsangabe</label>
        <input id="special_location_input" class="form-control" type="text" name="special_location" value="{{ $service->special_location }}"/>
        <input type="hidden" name="city_id" value="{{ $service->city_id }}"/>
    </div>
    <div class="form-group">
        <label for="time">Uhrzeit (leer lassen für Standarduhrzeit)</label>
        <input class="form-control" type="text" name="time" placeholder="HH:MM"
               value="{{ strftime('%H:%M', strtotime($service->time)) }}" @cannot('gd-allgemein-bearbeiten') disabled @endcannot />
    </div>
    <div class="form-group">
        <label for="participants[P][]"><span class="fa fa-user"></span>&nbsp;Pfarrer*in</label>
        <select class="form-control peopleSelect" name="participants[P][]" multiple @cannot('gd-pfarrer-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @if($service->pastors->contains($user)) selected @endif>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" name="need_predicant" value="1"
               id="needPredicant" @if ($service->need_predicant) checked @endif @cannot('gd-pfarrer-bearbeiten') disabled @endcannot >
        <label class="form-check-label" for="needPredicant">
            Für diesen Gottesdienst wird ein Prädikant benötigt.
        </label>
        <br /><br />
    </div>
    <div class="form-group">
        <label for="participants[O][]"><span class="fa fa-user"></span>&nbsp;Organist*in</label>
        <select class="form-control peopleSelect" name="participants[O][]" multiple @cannot('gd-organist-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @if($service->organists->contains($user)) selected @endif>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="participants[M][]"><span class="fa fa-user"></span>&nbsp;Mesner*in</label>
        <select class="form-control peopleSelect" name="participants[M][]" multiple @cannot('gd-mesner-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @if($service->sacristans->contains($user)) selected @endif>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="form-group">
        <label for="participants[A][]"><span class="fa fa-users"></span>&nbsp;Weitere Beteiligte</label>
        <select class="form-control peopleSelect" name="participants[A][]" multiple @cannot('gd-allgemein-bearbeiten') disabled @endcannot placeholder="Eine oder mehrere Personen (keine Anmerkungen!)">
            @foreach ($users as $user)
                <option value="{{ $user->id }}" @if($service->otherParticipants->contains($user)) selected @endif>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
</div>
