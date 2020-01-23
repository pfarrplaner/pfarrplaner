@extends('layouts.app')

@section('title') {{ $input->title }} bearbeiten @endsection

@section('content')
    <form method="post" action="{{ route('inputs.input', $input->getKey()) }}">
        <div class="card">
            <div class="card-body">
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
                <div class="form-group">
                    <label for="ministries">Folgende Dienste mit planen:</label>
                    <select id="selectMinistry" class="form-control" name="ministries[]" multiple>
                        <option value="P">Pfarrer*in</option>
                        <option value="O">Organist*in</option>
                        <option value="M">Mesner*in</option>
                        <option value="A">Weitere Beteiligte</option>
                        @foreach ($ministries as $ministry)
                            <option value="{{ $ministry }}">{{ $ministry }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Bearbeiten</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('#selectMinistry').selectize({
                create: true,
            });
        });
    </script>
@endsection
