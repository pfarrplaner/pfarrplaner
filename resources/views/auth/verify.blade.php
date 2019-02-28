@extends('layouts.app')
@section('title', 'Überprüfung')


@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Überprüfung Ihrer E-Mailadresse</div>

                <div class="card-body">
                    @if (session('resent'))
                        <div class="alert alert-success" role="alert">
                            Ein neuer Bestätigungslink wurde an Ihre E-Mailadresse gesendet.
                        </div>
                    @endif

                    Bevor es weitergeht, prüfen Sie bitte Ihren Posteingang. Dort sollte sich eine Nachricht mit
                        einem Bestätigunglink befinden. Wenn Sie diese Nachricht nicht erhalten haben,
                        <a href="{{ route('verification.resend') }}">klicken Sie hier, um Sie erneut zu senden.</a>.
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
