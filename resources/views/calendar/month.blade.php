@extends('layouts.app', ['noNavBar' => $slave])


@section('title')
    {{ $months[(int)$month] }} {{ $year }}
@endsection

@section('navbar-left')
    @include('calendar.partials.navbar')
@endsection

@section('content')
    @if($orientation == 'vertical')
        @include('calendar.partials.month_vertical')
    @else
        @include('calendar.partials.month_horizontal')
    @endif
@endsection

@section('scripts')
    @include('calendar.partials.scripts')
@endsection

@section('control-sidebar')
    @include('calendar.partials.control-sidebar')
@endsection