@extends('layouts.app')

@section('title', '{{ $input->title }} bearbeiten')

@section('content')
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                {{ $input->title }}
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div><br/>
                @endif
                    <form method="post" action="{{ route('inputs.input', $input->getKey()) }}">
                        @csrf
                        <div class="form-group"> <!-- Radio group !-->
                            <label class="control-label">{{ $input->title }} für folgende Kirchengemeinde bearbeiten:</label>
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
                        <button type="submit" class="btn btn-primary">Bearbeiten</button>
                    </form>
            </div>
        </div>
    </div>
@endsection
