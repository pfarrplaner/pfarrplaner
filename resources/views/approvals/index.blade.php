@extends('layouts.app')

@section('title', 'Urlaubsanträge')

@section('content')
    @component('components.ui.card')
        @if(count($absences) == 0)
            <p>Es liegen aktuell keine unbearbeiteten Urlaubsanträge vor.</p>
        @else
            <table class="table table-striped table-hover">
                <thead>
                <tr>
                    <th>Antragsteller</th>
                    <th>Urlaubsantrag</th>
                    <th>Vertretungen</th>
                    <th>Genehmigungen</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php /** @var \App\Absence $absence */ ?>
                @foreach($absences as $absence)
                    <tr>
                        <td>
                            {{ $absence->user->fullName() }}
                        </td>
                        <td>
                            {{ $absence->from->format('d.m.Y') }} - {{ $absence->to->format('d.m.Y') }}
                            ({{ $absence->to->diff($absence->from)->days+1 }} Tage)<br/>
                            {{ $absence->reason }}
                        </td>
                        <td>
                            @if(count($absence->replacements))
                                @foreach($absence->replacements as $replacement)
                                    {{ $replacement->from }}
                                    - {{ $replacement->to }} {{ $replacement->users->pluck('fullName')->join(',') }}
                                @endforeach
                            @else
                                keine
                            @endif
                        </td>
                        <td>
                            @foreach($absence->user->approvers as $approver)
                                <?php $approval = $absence->approvals()->where('user_id', $approver->id)->first() ?>
                                @if((null !== $approval))
                                    @if($approval->status=='approved')<span class="fa fa-check-circle"
                                                                            style="color: green;"></span> @else <span
                                            class="fa fa-times-circle" style="color: red;"></span> @endif
                                @else
                                    <span class="fa fa-question-circle" style="color: orange;"></span>
                                @endif
                                {{ $approver->fullName() }}<br/>
                            @endforeach
                        </td>
                        <td class="text-right">
                            @if(null === $absence->approvals()->where('user_id', Auth::user()->id)->first())
                                <a class="btn btn-success" href="{{ route('absence.approve', $absence) }}"><span
                                            class="fa fa-edit"></span> Annehmen</a>
                                <a class="btn btn-danger" href="{{ route('absence.reject', $absence) }}"><span
                                            class="fa fa-edit"></span> Ablehnen</a>
                            @endif
                            <a class="btn btn-secondary"
                               href="{{ route('absences.index', ['year' => $absence->from->year, 'month' => $absence->from->month]) }}">
                                <span class="fa fa-calendar"></span> Im Urlaubskalender ansehen
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        @endif
    @endcomponent
@endsection