@extends('layouts.app')

@section('content')
    <div class="container py-5">
        @foreach($reports as $group => $reportGroup)
            <div class="card">
                <div class="card-header">
                    {{ $group }}
                </div>
                <div class="card-body">
                    @foreach($reportGroup as $report)
                        <div class="row">
                            <div class="col-1">
                                <span class="{{ $report->icon }} fa-3x"></span><br /><br />
                            </div>
                            <div class="col-11">
                                <b><a href="{{ route('reports.setup', $report->getKey()) }}">{{ $report->title }}</a></b><br/>{{ $report->description }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <br />
        @endforeach
    </div>
@endsection
