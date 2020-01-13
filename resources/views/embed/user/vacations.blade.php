@if(count($vacations))

    <div class="row ctype-textbox listtype-none showmobdesk-0">
        <div id="c1050561" class="col s12 bullme ">
            <div class="card-panel default">
                <p class="bodytext" style="margin-top:5px;"><b>Bitte beachten Sie:</b> {{ $user->fullName(true) }} ist an folgenden Tagen nicht erreichbar und wird von Kollegen vertreten:</p>
                <table class="" style="margin-top: 10px;">
                    <thead>
                    <th>Datum</th>
                    <th>Vertretung</th>
                    </thead>
                    <tbody>
                    @foreach($vacations->sortBy('from') as $vacation)
                        @foreach($vacation->replacements->sortBy('from') as $replacement)
                        <tr>
                            <td valign="top" style="vertical-align: top;">{{ $replacement->from->format('d.m.Y') }} @if($replacement->to > $replacement->from)- {{ $replacement->to->format('d.m.Y') }} @endif</td>
                            <td valign="top">
                                    @foreach($replacement->users as $user)
                                        <b>{{ $user->fullName() }}</b>
                                        @if($user->office)<br />{{ $user->office }}@endif
                                        @if($user->address)<br />{{ $user->address }}@endif
                                        @if($user->phone)<br />Tel. {{ $user->phone }}@endif
                                        <br />E-Mail <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    @endforeach
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


@endif