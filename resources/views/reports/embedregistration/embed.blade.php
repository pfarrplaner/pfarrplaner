<div id="{{ $randomId }}">
    <div id="{{ $randomId }}_form" class="row ctype-textbox listtype-none showmobdesk-0">
        @foreach($errors->all() as $error)
            <div
                style="border-radius: 3px; margin: 3px 10px; padding: 3px; background-color:red; color:white;">{{ $error }}</div>
        @endforeach
        @foreach($success->all() as $message)
            <div
                style="border-radius: 3px; margin: 3px 10px; padding: 3px; background-color:green; color:white;">{{ $message }}</div>
        @endforeach
        <div id="{{ $randomId }}" class="col s12 bullme ">
            @if(count($services))
                @foreach ($services as $dayServices)
                    @if(!$singleService)
                    <h2 style="margin-bottom: 20px;">Gottesdienste am {{ $dayServices[0]->day->date->format('d.m.Y') }} ({{ \App\Liturgy::getDayInfo($dayServices[0]->day)['title'] }})</h2>
                    @endif
                    @foreach($dayServices as $service)
                        <div class="card-panel default registrable-service" id="{{ $randomId }}-{{ $loop->index }}">
                            <h3>{{ $service->titleText(false) }}</h3>
                            <table>
                                <tr>
                                    <td valign="top">
                                        {{ $service->day->date->format('d.m.Y') }}, {{ $service->timeText() }}<br/>
                                        {{ $service->locationText() }}<br/><br/>
                                        {{ $service->descriptionText() }}
                                    </td>
                                    <td valign="top" style="text-align: right;">
                                        @if($service->registration_active)
                                            @if(isset($service->registration_online_start) && ($service->registration_online_start > \Carbon\Carbon::now()))
                                                Reservierung erst ab {{ $service->registration_online_start->format('d.m.Y, H:i') }} Uhr möglich
                                            @elseif(isset($service->registration_online_end) && ($service->registration_online_end < \Carbon\Carbon::now()))
                                                <b>Keine Online-Anmeldung mehr möglich.</b>
                                                @if($service->getSeatFinder()->remainingCapacity() > 0)
                                                    @if($service->registration_phone)
                                                        <br /><small>Evtl. ist unter {{ $service->registration_phone }} noch eine telefonische Anmeldung möglich.</small>
                                                    @endif
                                                @endif
                                            @else
                                                @if($service->getSeatFinder()->remainingCapacity() > 0)
                                                    {!! $service->getSeatFinder()->remainingCapacityText('noch ca. %s Plätze<b>*</b>') !!}<br/>
                                                <a class="btn btn-secondary show-reg-form" href="#"
                                                   data-container="#{{ $randomId }}-{{ $loop->index }}">Anmelden</a>
                                                @else
                                                    komplett ausgebucht
                                                @endif
                                            @endif
                                        @else
                                            <b>Keine Online-Anmeldung mehr möglich.</b>
                                            @if($service->getSeatFinder()->remainingCapacity() > 0)
                                                @if($service->registration_phone)
                                                    <br /><small>Evtl. ist unter {{ $service->registration_phone }} noch eine telefonische Anmeldung möglich.</small>
                                                @endif
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                            </table>
                            <div class="reg-form" id="{{ $randomId }}-{{ $loop->index }}-reg" style="display: none;"
                                 data-service="{{ $service->id }}">
                                <label for="name">Nachname</label>
                                <input name="name" value="" type="text"/>
                                <label for="first_name">Vorname</label>
                                <input name="first_name" value="" type="text"/>
                                <label for="street">Straße, Hausnummer</label>
                                <input name="street" value="" type="text"/>
                                <label for="zip">Postleitzahl</label>
                                <input name="zip" value="" type="text"/>
                                <label for="city">Ort</label>
                                <input name="city" value="" type="text"/>
                                <label for="phone">Telefonnummer</label>
                                <input name="phone" value="" type="text"/>
                                <label for="email">E-Mailadresse (falls vorhanden)</label>
                                <input name="email" value="" type="text" />
                                <label for="number">Anzahl Personen</label>
                                <input name="number" value="1" type="text"/><br/><br/>
                                <a class="btn btn-secondary submit-reg-form" href="#"
                                        style="width: auto; height: auto; position: relative; background-color: none;"
                                   data-container="#{{ $randomId }}-{{ $loop->index }}">Anmeldung absenden</a>
                                <br/>
                                <small>Die Erhebung dieser Daten erfolgt nach §6 Abs. 1 CoronaVO. Nach §6 Abs. 4-5 CoronaVO
                                    können Sie nur
                                    nach korrekter Angabe Ihrer Kontaktdaten am Gottesdienst teilnehmen. Wir bitten hierfür
                                    um
                                    Verständnis.
                                </small>
                            </div>
                        </div>
                    @endforeach
                @endforeach
            @else
                <b>Aktuell gibt es keine Gottesdienste, für die eine Anmeldung benötigt wird.</b>
            @endif
        </div>
    </div>
    @if($noScript == 0)
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
    <script defer>
        var blockSubmission = false;

        $(document).ready(function () {
            $('#{{ $randomId }} .show-reg-form').click(function () {
                $('.registrable-service').hide();
                $($(this).data('container')).show();
                $($(this).data('container') + '-reg').show();
                $(this).hide();
                $($(this).data('container') + '-reg input[name="name"]').focus();
            });

            $('#{{ $randomId }} .submit-reg-form').click(function (event) {
                event.preventDefault();
                var url = '{{ $url }}&cities={{ join(',', $cities) }}';
                var check = true;
                ['name', 'first_name', 'street', 'zip', 'city', 'phone', 'number'].forEach(element => {
                    if ($($(this).data('container') + '-reg input[name="' + element + '"]').val() == '') {
                        alert('Das Feld "' + $('label[for="' + element + '"]').html() + '" darf nicht leer bleiben');
                        check = false;
                    }
                });
                if (check) {

                    url += '&service=' + encodeURI($($(this).data('container') + '-reg').data('service'));
                    @if ($singleService) url += '&singleService={{ $singleService }}'; @endif
                    @if ($noScript) url += '&noScript=1'; @endif

                    ['name', 'first_name', 'number', 'email'].forEach(element => {
                        if (element != 'contact') {
                            url += '&' + element + '=' + encodeURI($($(this).data('container') + '-reg input[name="' + element + '"]').val())
                        } else {
                            url += '&' + element + '=' + encodeURI($($(this).data('container') + '-reg textarea[name="' + element + '"]').val())
                        }
                    });
                    url += '&contact=' + encodeURI(
                        $($(this).data('container') + '-reg input[name="street"]').val() + "\n"
                        + $($(this).data('container') + '-reg input[name="zip"]').val() + " "
                        + $($(this).data('container') + '-reg input[name="city"]').val() + "\n"
                        + $($(this).data('container') + '-reg input[name="phone"]').val()
                    );
                    if (!blockSubmission) {
                        blockSubmission = true;
                        fetch(url)
                            .then((res) => {
                                return res.text();
                            })
                            .then((data) => {
                                $('#{{ $randomId }}').html(data);
                                blockSubmission = false;
                            });
                    }

                }
            });
        });
    </script>
    @endif
</div>
