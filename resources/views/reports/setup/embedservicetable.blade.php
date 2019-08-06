@extends('layouts.app')

@section('title', 'Programm für die Kinderkirche erstellen')

@section('content')
    <div class="container py-5">
        <div class="card">
            <div class="card-header">
                HTML-Code für eine Gottesdiensttabelle erstellen
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
                <form method="post" action="{{ route('reports.render', $report) }}">
                    @csrf
                    <div class="form-group">
                        <label class="control-label">Art der Liste:</label>
                        <select class="form-control" name="listType">
                            <option value="table-cities">Gottesdienste in einer oder mehreren Kirchengemeinden</option>
                            <option value="table-locations">Gottesdienste in einer oder mehreren Kirchen</option>
                            <option value="table-cc">Kinderkirche in einer oder mehreren Kirchengemeinden</option>
                        </select>
                    </div>
                    <div class="form-group form-group-hideable for-table-cities for-table-cc">
                        <label class="control-label">Folgende Kirchengemeinden mit einbeziehen:</label>
                        @foreach ($cities as $city)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $city->id }}"
                                       id="ids{{$city->id}}" checked>
                                <label class="form-check-label" for="#ids{{$city->id}}">
                                    {{$city->name}}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group form-group-hideable for-table-locations">
                        <label class="control-label">Folgende Kirchen mit einbeziehen:</label>
                        @foreach ($locations as $location)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="ids[]" value="{{ $location->id }}"
                                       id="defaultCheck{{$location->id}}" checked>
                                <label class="form-check-label" for="#ids{{$location->id}}">
                                    {{$location->name}}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    <div class="form-group">
                        <label for="cors-origin">Aufrufende Website:</label>
                        <input type="text" class="form-control" name="cors-origin" value="" placeholder="z.B. https://www.tailfingen.de/" />
                    </div>
                    <div class="form-group">
                        <label for="limit">Max. Anzahl der Gottesdienste:</label>
                        <input type="text" class="form-control" name="limit" value="5" placeholder="max. Anzahl, z.B. 5" />
                    </div>
                    <button type="submit" class="btn btn-primary">Erstellen</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        function adjustForm() {
            var showGroup = '.for-' + $('select[name=listType]').val();
            $('.form-group-hideable').hide();
            $('.form-group-hideable input').attr('disabled', 'disabled');
            $(showGroup).show();
            $(showGroup+' input').removeAttr('disabled');
        }

        $(document).ready(function(){
            $('select[name=listType]').on('change', function(){
                adjustForm();
            });

            adjustForm();
        });

    </script>
@endsection
