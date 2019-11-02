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
                <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'configure']) }}">
                    @csrf
                    <div class="form-group"> <!-- Radio group !-->
                        <label class="control-label">Bekanntgaben für folgende Kirchengemeinde erstellen:</label>
                        <select class="form-control" name="city">
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">
                                    {{$city->name}}
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
