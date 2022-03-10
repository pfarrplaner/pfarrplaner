@extends('layouts.app')

@section('title', 'Bekanntgaben erstellen')

@section('content')
    <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'input']) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Bekanntgaben für folgenden Gottesdienst erstellen:</label>
                    <select class="form-control" name="service">
                        @foreach ($services as $service)
                            <option value="{{ $service->id }}">
                                {{$service->date->format('d.m.Y')}}, {{ $service->timeText() }}
                                , {{ $service->locationText() }}
                            </option>
                        @endforeach
                    </select>
                    @component('components.validation', ['name' => 'service']) @endcomponent
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Weiter &gt;</button>
            </div>
        </div>
    </form>
@endsection
