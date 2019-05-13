<div role="tabpanel" class="tab-pane fade @if($tab == 'rites') in active show @endif " id="rites">
    <div id="ritesAlert" style="display: none;" class="alert alert-danger">Kasualien können nicht bearbeitet werden, solange ungespeicherte Änderungen existieren. Bitte speichere zunächst den Gottesdienst.</div>
    @if ($service->weddings->count() >0 )
        <h3>{{ $service->weddings->count() }} @if($service->weddings->count() != 1)Trauungen @else Trauung @endif</h3>
        @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten'])
            <table class="table table-fluid">
                <tr>
                    <th>Ehepartner 1</th>
                    <th>Ehepartner 2</th>
                    <th>Traugespräch</th>
                    <th>Anmeldung</th>
                    <th>Urkunden</th>
                    <th></th>
                </tr>
                @foreach($service->weddings as $wedding)
                    <tr>
                        @include('partials.wedding.details', ['wedding' => $wedding])
                        <td>
                            @can('gd-kasualien-bearbeiten')
                                <a class="btn btn-default btn-secondary btn-rite" href="{{ route('weddings.edit', $wedding->id) }}" title="Trauung bearbeiten"><span class="fa fa-edit"></span></a>
                                <a class="btn btn-default btn-danger btn-rite" href="{{ route('wedding.destroy', $wedding->id) }}" title="Trauung löschen"><span class="fa fa-trash"></span></a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </table>
        @endcanany
    @endif
    @if ($service->baptisms->count() >0 )
        <h3>{{ $service->baptisms->count() }} @if($service->baptisms->count() != 1)Taufen @else Taufe @endif</h3>
        @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten'])
            <table class="table table-fluid">
                <tr>
                    <th>Täufling</th>
                    <th>Erstkontakt</th>
                    <th>Taufgespräch</th>
                    <th>Anmeldung</th>
                    <th>Urkunden</th>
                    <th></th>
                </tr>
                @foreach($service->baptisms as $baptism)
                    <tr>
                        @include('partials.baptism.details', ['baptism' => $baptism])
                        <td>
                            @can('gd-kasualien-bearbeiten')
                                <a class="btn btn-default btn-secondary btn-rite" href="{{ route('baptisms.edit', $baptism->id) }}" title="Taufe bearbeiten"><span class="fa fa-edit"></span></a>
                                <a class="btn btn-default btn-danger btn-rite" href="{{ route('baptism.destroy', $baptism->id) }}" title="Taufe löschen"><span class="fa fa-trash"></span></a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </table>
        @endcanany
    @endif
    @if ($service->funerals->count() > 0)
        <h3>{{ $service->funerals->count() }} @if($service->funerals->count() != 1)Bestattungen @else Bestattung @endif</h3>
        @canany(['gd-kasualien-lesen','gd-kasualien-bearbeiten'])
            <table class="table table-fluid">
                <tr>
                    <th>Person</th>
                    <th>Bestattungsart</th>
                    <th>Abkündigung</th>
                    <th></th>
                </tr>
                @foreach ($service->funerals as $funeral)
                    <tr>
                        @include('partials.funeral.details', ['funeral' => $funeral])
                        <td>
                            @can('gd-kasualien-bearbeiten')
                                <a class="btn btn-default btn-secondary btn-rite" href="{{ route('funerals.edit', $funeral->id) }}" title="Bestattung bearbeiten"><span class="fa fa-edit"></span></a>
                                <a class="btn btn-default btn-danger btn-rite" href="{{ route('funeral.destroy', $funeral->id) }}" title="Bestattung löschen"><span class="fa fa-trash"></span></a>
                            @endcan
                        </td>
                    </tr>
                @endforeach
            </table>
        @endcanany
    @endif
    @can('gd-kasualien-bearbeiten')
        <div id="rites-buttons">
            <a class="btn btn-default btn-secondary btn-rite" href="{{ route('wedding.add', $service->id) }}">Trauung hinzufügen</a>
            <a class="btn btn-default btn-secondary btn-rite" href="{{ route('baptism.add', $service->id) }}">Taufe hinzufügen</a>
            <a class="btn btn-default btn-secondary btn-rite" href="{{ route('funeral.add', $service->id) }}">Bestattung hinzufügen</a>
        </div>
    @endcan
</div>
