@extends('layouts.app')

@section('title') Mehrere Gottesdienste anlegen ({{ $from->format('d.m.Y') }}-{{ $to->format('d.m.Y') }}) @endsection

@section('content')
    <form method="post" action="{{ route('inputs.save', $input->getKey()) }}">
        <input type="hidden" name="title" value="{{ $title }}" />
        <div class="card">
            <div class="card-body">
                @csrf
                <table class="table table-fluid">
                    <thead>
                    <tr>
                        <th>Datum</th>
                        <th>Uhrzeit</th>
                        <th>Kirche</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($services as $service)
                        <tr>
                            <td>
                                {{ $service['date']->format('d.m.Y') }}
                            </td>
                            <td>
                                <input type="text" class="form-control"
                                       name="service[{{$service['date']->format('d.m.Y')}}][{{ $service['index'] }}][time]"
                                       value="{{ substr($service['location']->default_time, 0, 5) }}"
                                       placeholder="HH:MM"/>
                            </td>
                            <td>
                                {{ $service['location']->name }}
                                <input type="hidden"
                                       name="service[{{$service['date']->format('d.m.Y')}}][{{ $service['index'] }}][location]"
                                       value="{{ $service['location']->id }}"/>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger remove-row"><span class="fa fa-trash"></span>
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Gottesdienste anlegen</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.remove-row').click(function () {
                $(this).parent().parent().remove();
            });
        });
    </script>
@endsection
