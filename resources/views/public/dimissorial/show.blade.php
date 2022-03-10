@extends('layouts.public')

@section('title')Dimissoriale @endsection

@section('contents')
    <main class="container mt-2 ">
    <div class="card">
        <div class="card-header">Dimissoriale für eine {{ $type }} erteilen</div>
        <div class="card-body">
            @if(!$rite->dimissorial_received)
                <p>Sie wurden gebeten, ein Dimissoriale für die {{ $type }} von
            @if ($type=='Taufe') <b>{{ $rite->candidate_name }}</b> @endif
            @if ($type=='Beerdigung')<b>{{ $rite->buried_name }}</b> @if($rite->dob && $rite->dod)({{ $rite->dob->format('d.m.Y') }}-{{ $rite->dod->format('d.m.Y') }}) @endif @endif
            @if ($type=='Trauung')
                    <span style="{{ $spouse == 1 ? 'font-weight: bold' : '' }}">{{ $rite->spouse1_name }}</span> &amp;
                    <span style="{{ $spouse == 2 ? 'font-weight: bold' : '' }}">{{ $rite->spouse2_name }}</span>
            @endif
                    @if ($rite->service) am {{ $rite->service->date->format('d.m.Y') }} um {{ $rite->service->timeText() }}
                    (Kirchengemeinde: {{ $rite->service->city->name }}, {{ $rite->service->participantsText('P', true) }}) @endif
                    zu erteilen.</p>
                <p>Wenn Sie das Dimissoriale hiermit erteilen wollen, klicken Sie hier:</p>
                <div class="mt-1 mb-4">
                    <form method="post" action="{{ route('dimissorial.grant', ['type' => lcfirst($type), 'id' => $id, 'spouse' => $spouse]) }}">
                        @csrf
                        <input type="submit" class="btn btn-primary" title="Dimissoriale erteilen" value="Dimissoriale erteilen" />
                    </form>
                </div>
                @if ($rite->service)
                <p>Wenn Sie das Dimissoriale nicht erteilen wollen oder Fragen zur {{ $type }} haben, wenden Sie sich bitte an:</p>
                @endif
            @else
                <p>Sie haben bereits ein Dimissoriale für diese {{ $type }} erteilt. Es gibt nichts mehr weiter zu tun.</p>
                @if ($rite->service)
                <p>Wenn Sie der Meinung sind, dass hier ein Fehler vorliegt oder noch Fragen zur {{ $type }} haben, wenden Sie sich bitte an: </p>
                @endif
            @endif
                @if ($rite->service)
                <table class="table table-striped">
                    <thead>
                    <tr style="display:none;">
                        <th>Name</th>
                        <th>Telefon</th>
                        <th>E-Mailadresse</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($rite->service->pastors as $person)
                        <tr>
                            <td>{{ $person->fullName(true) }}</td>
                            <td>@if($person->phone)<a href="tel:{{ $person->phone }}">{{ $person->phone }}</a>@endif</td>
                            <td>@if($person->email)<a href="mailto:{{ $person->email }}">{{ $person->email }}</a>@endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                @endif
        </div>
    </div>
    </main>
@endsection
