@extends('layouts.app')

@section('title', 'Dienstplan für einen Dienst erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Plan für folgende Kirchengemeinde erstellen:</label>
                    <select class="form-control" name="city">
                        @foreach ($cities as $city)
                            <option value="{{ $city->id }}">
                                {{$city->name}}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="ministries">Folgenden Dienstplan erstellen:</label>
                    <select id="selectMinistry" class="form-control" name="ministry">
                        @foreach ($ministries as $ministryKey => $ministry)
                            <option value="{{ $ministryKey }}">{{ $ministry }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="start">Gottesdienste von:</label>
                    <input type="text" class="form-control datepicker" name="start" value="{{ date('d.m.Y') }}"
                           placeholder="TT.MM.JJJJ"/>
                </div>
                <div class="form-group">
                    <label for="end">Bis:</label>
                    <input type="text" class="form-control datepicker" name="end" value="{{ $maxDate->date->format('d.m.Y') }}"
                           placeholder="TT.MM.JJJJ"/>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection
