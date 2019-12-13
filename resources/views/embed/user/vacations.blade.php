@if(count($vacations))

    <div class="row ctype-textbox listtype-none showmobdesk-0">
        <div id="c1050561" class="col s12 bullme ">
            <div class="card-panel default">
                <p class="bodytext" style="margin-top:5px;"><b>Bitte beachten Sie:</b> {{ $vacations[0]['away']->fullName(true) }} ist an folgenden Tagen nicht erreichbar und wird von Kollegen vertreten:</p>
                <table class="" style="margin-top: 10px;">
                    <thead>
                    <th>Datum</th>
                    <th>Vertretung</th>
                    </thead>
                    <tbody>
                    @foreach($vacations as $vacation)
                        <tr>
                            <td>{{ $vacation['start']->format('d.m.Y') }} @if($vacation['end'] > $vacation['start'])- {{ $vacation['end']->format('d.m.Y') }} @endif</td>
                            <td>
                                @foreach($vacation['substitute'] as $sub)
                                    <b>{{ $sub->fullName(true) }}</b><br />
                                    {{ $sub->office }}<br />
                                    Tel. {{ $sub->phone }}<br />
                                    E-Mail <a href="mailto:{{$sub->email}}">{{ $sub->email }}</a> @if (!$loop->last) <br /><br />@endif
                                @endforeach
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endif