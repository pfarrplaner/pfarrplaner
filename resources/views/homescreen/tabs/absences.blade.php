<div role="tabpanel" class="tab-pane fade" id="absences">
    <div>
        <h2 style="width: 100%">Mein Urlaub
        </h2>
        <a href="{{ route('absences.index') }}" class="btn btn-sm btn-primary pull-right"><span
                    class="fa fa-calendar"></span> Urlaubskalender öffnen</a>
    </div>
    @if (count($absences))
        <hr/>
        <h4>Urlaub / Abwesenheit</h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <td>Zeitraum</td>
                    <td>Beschreibung</td>
                    <td>Vertretung</td>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                @foreach($absences as $absence)
                    <tr>
                        <td>{{ $absence->durationText() }}</td>
                        <td>{{ $absence->reason }}</td>
                        <td>{{ $absence->replacementText() }}</td>
                        <td style="text-align: right;">
                            <a href="{{ route('absences.index') }}" class="btn btn-sm btn-secondary"
                               title="Im Kalender ansehen"><span class="fa fa-calendar"></span></a>
                            <a href="{{ route('absences.edit', $absence->id) }}" class="btn btn-sm btn-primary"
                               title="Bearbeiten"><span class="fa fa-edit"></span></a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
    @if (count($replacements))
        <hr/>
        <h4>Vertretungen</h4>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                <tr>
                    <td>Zeitraum</td>
                    <td>Vertretung für</td>
                    <td>Beschreibung</td>
                    <td>Vertretungsregelung</td>
                    <td></td>
                </tr>
                </thead>
                <tbody>
                @foreach($replacements as $replacement)
                    @if (is_object($replacement->absence))
                    <tr>
                        <td>{{ $replacement->absence->durationText() }}</td>
                        <td>{{ $replacement->absence->user->fullName() }}</td>
                        <td>{{ $replacement->absence->reason }}</td>
                        <td>{{ $replacement->absence    ->replacementText() }}</td>
                        <td style="text-align: right;">
                            <a href="{{ route('absences.index') }}" class="btn btn-sm btn-secondary"
                               title="Im Kalender ansehen"><span class="fa fa-calendar"></span></a>
                            @can('update', $replacement->absence)
                                <a href="{{ route('absences.edit', $absence->id) }}" class="btn btn-sm btn-primary"
                                   title="Bearbeiten"><span class="fa fa-edit"></span></a>
                            @endcan
                        </td>
                    </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>