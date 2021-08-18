@extends('layouts.public')

@section('title')Dimissoriale erteilt @endsection

@section('contents')
    <main class="container mt-2 ">
    <div class="card">
        <div class="card-header">Dimissoriale für eine {{ $type }} erteilen</div>
        <div class="card-body">
            <p>Herzlichen Dank!</p>
            <p>Wenn Sie noch Fragen zur {{ $type }} haben, wenden Sie sich bitte an:</p>
                <table class="table table-striped">
                    <thead>
                    <tr style="display:none;">
                        <th>Name</th>
                        <th>Telefon</th>
                        <th>E-Mailadresse</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($rite->service->pastors as $person)
                        <tr>
                            <td>{{ $person->fullName(true) }}</td>
                            <td>@if($person->phone)<a href="tel:{{ $person->phone }}">{{ $person->phone }}</a>@endif</td>
                            <td>@if($person->email)<a href="mailto:{{ $person->email }}">{{ $person->email }}</a>@endif</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
        </div>
    </div>
    </main>
@endsection
