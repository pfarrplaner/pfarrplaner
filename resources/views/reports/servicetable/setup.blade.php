@extends('layouts.app')

@section('title', 'Jahresplan der Gottesdienste erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Jahresplan für folgende Kirchengemeinde erstellen:</label>
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
                <div class="form-group">
                    <label for="ministries">Folgende Dienste mit einschließen:</label>
                    <select id="selectMinistry" class="form-control fancy-selectize" name="ministries[]" multiple>
                        @foreach (\App\Ministry::all() as $ministry)
                            <option value="{{ $ministry }}">{{ $ministry }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Namen anzeigen als:</label>
                    <?php $nameFormat = Auth::user()->getSetting('calendar_name_format'); ?>
                    <select name="name_format" class="form-control">
                        <option @if($nameFormat == \App\Http\Controllers\CalendarController::NAME_FORMAT_DEFAULT)selected @endif value="{{ \App\Http\Controllers\CalendarController::NAME_FORMAT_DEFAULT }}">Pfr. Müller</option>
                        <option @if($nameFormat == \App\Http\Controllers\CalendarController::NAME_FORMAT_INITIAL_AND_LAST)selected @endif value="{{ \App\Http\Controllers\CalendarController::NAME_FORMAT_INITIAL_AND_LAST }}">Pfr. K. Müller</option>
                        <option @if($nameFormat == \App\Http\Controllers\CalendarController::NAME_FORMAT_FIRST_AND_LAST)selected @endif value="{{ \App\Http\Controllers\CalendarController::NAME_FORMAT_FIRST_AND_LAST }}">Pfr. Karl Müller</option>
                    </select>

                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection
