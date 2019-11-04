@if (!$parish)
    <div id="{{ $randomId }}" class="row ctype-textbox listtype-none showmobdesk-0">
        <div id="c1050561" class="col s12 bullme ">
            <div class="card-panel default">

                <p class="bodytext"><b>Ihr Ansprechpartner</b></p>
                <p class="bodytext">Damit wir einen Ansprechpartner für Sie ermitteln können, geben Sie bitte hier Ihre
                    Adresse ein:</p>
                <div>
                    <label for="street">Straße:</label>
                    <select id="street">
                        @foreach ($streets as $street)
                            <option>{{ $street }}</option>
                        @endforeach
                    </select><br/>
                    <label for="number">Hausnummer</label>
                    <input id="number" value="" type="text"/>
                    <button id="btnFind">Ansprechpartner finden</button>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.min.js" type="text/javascript"></script>
    <script defer>
        $(document).ready(function () {
            $('#btnFind').click(function () {
                var url = '{{ $url }}&street=' + $('#street').val() + '&number=' + $('#number').val();
                if (($('#street').val() != '') && ($('number').val() != '')) {
                    $('#{{ $randomId }}').html('Bitte warten, Daten werden übertragen...');
                    fetch(url)
                        .then((res) => {
                            return res.text();
                        })
                        .then((data) => {
                            $('#{{ $randomId }}').html(data);
                        });
                }
            })

        });
    </script>
@else
    <div id="{{ $randomId }}" class="row ctype-textbox listtype-none showmobdesk-0">
        <div id="c1050561" class="col s12 bullme ">
            <div class="card-panel default">

                <p class="bodytext"><b>Bitte wenden Sie sich an:</b></p>
                @if(count($parish->users))
                    <br/>
                    @foreach ($parish->users as $user)
                        <div>
                            @if ($user->image)
                                <div style="float: left; width: 90px;">
                                    <img width="80px" src="{{ url('storage/'.$user->image) }}"/>
                                </div>
                            @endif
                            <div style="float:left;">
                                <p class="bodytext"><b>{{ $user->fullName(true) }}</b><br/>
                                    {{ $parish->name }}<br/>
                                    {{ $parish->address }}<br/>
                                    {{ $parish->zip }} {{ $parish->city }}<br/>
                                    Fon {{ $parish->phone }}<br/>E-Mail <a
                                            href="mailto:{{ $parish->email }}">{{ $parish->email }}</a>
                                </p>
                            </div>
                        </div>
                        <div style="clear: both;"></div>
                    @endforeach
                @else
                    <p class="bodytext"><b>{{ $parish->name }}</b><br/>
                        {{ $parish->address }}<br/>
                        {{ $parish->zip }} {{ $parish->city }}<br/>
                        Fon {{ $parish->phone }}<br/>E-Mail <a
                                href="mailto:{{ $parish->email }}">{{ $parish->email }}</a>
                @endif
            </div>
        </div>
    </div>
    <script defer>
        localStorage.setItem('parish', '{{ $parish->id }}');
    </script>
@endif