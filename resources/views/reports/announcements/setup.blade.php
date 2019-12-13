@extends('layouts.app')

@section('title', 'Bekanntgaben erstellen')

@section('content')
    <form method="post" action="{{ route('report.step', ['report' => $report, 'step' => 'configure']) }}">
        <div class="card">
            <div class="card-body">
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
                    @component('components.validation', ['name' => 'city']) @endcomponent
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Weiter &gt;</button>
            </div>
        </div>
    </form>
@endsection
