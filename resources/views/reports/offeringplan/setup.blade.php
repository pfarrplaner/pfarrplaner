@extends('layouts.app')

@section('title', 'Opferplan ausgeben')

@section('content')
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                Opferplan ausgeben
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('reports.render', $report) }}">
                    @csrf
                    <div class="form-group"> <!-- Radio group !-->
                        <label class="control-label">Opferplan für folgende Kirchengemeinde erstellen:</label>
                        <select class="form-control" name="city">
                            @foreach ($cities as $city)
                                <option value="{{ $city->id }}">
                                    {{$city->name}}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="year">Jahr:</label>
                        <select class="form-control" name="year">
                            @for ($year=$minDate->date->year; $year<=$maxDate->date->year; $year++)
                                <option value="{{ $year }}" @if ($year == date('Y')) selected @endif>{{ $year }}</option>
                            @endfor
                        </select>
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    </div>
@endsection
