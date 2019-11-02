@extends('layouts.app')

@section('title', 'Bekanntgaben erstellen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Bekanntgaben erstellen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'input']) }}">
                    @csrf
                    <div class="form-group"> <!-- Radio group !-->
                        <label class="control-label">Bekanntgaben für folgenden Gottesdienst erstellen:</label>
                        <select class="form-control" name="service">
                            @foreach ($services as $service)
                                <option value="{{ $service->id }}">
                                    {{$service->day->date->format('d.m.Y')}}, {{ $service->timeText() }}, {{ $service->locationText() }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Weiter &gt;</button>
                </form>
            </div>
        </div>
    @endcomponent
@endsection
