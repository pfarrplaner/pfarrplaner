@extends('layouts.app')

@section('title', 'Anmelden')

@section('content')
    @component('components.container')
        <form method="POST" action="{{ route('login') }}">
            @csrf
            @component('components.ui.card')
                @slot('cardHeader')
                    Anmelden
                @endslot
                @slot('cardFooter')
                    <button type="submit" class="btn btn-primary">
                        Anmelden
                    </button>

                    @if (Route::has('password.request'))
                        <a class="btn btn-link" href="{{ route('password.request') }}">
                            Passwort vergessen?
                        </a>
                    @endif
                @endslot
                    @input(['name' => 'email', 'label' => 'E-Mailadresse', 'value' => old('email'), 'type' => 'email', 'id' => 'email'])
                    @input(['name' => 'password', 'label' => 'Passwort', 'type' => 'password'])
                    @checkbox(['name' => 'remember', 'label' => 'Angemeldet bleiben', 'value' => old('remember')])
            @endcomponent
            @component('components.ui.card')
                @slot('collapseId')contactInfo @endslot
                @slot('cardHeader')
                    Anbieterinformationen
                @endslot
                <p><b>pfarrplaner.de</b> wird betrieben von:</p>
                <p>Pfarrer Christoph Fischer<br />für den Evangelischen Kirchenbezirk Balingen<br />Liegnitzer Str. 38<br />72461 Albstadt<br />
                    <br />
                    Fon 07432 3762<br />
                    Fax 07432 171760<br />
                    E-Mail <a href="mailto:christoph.fischer@elkw.de">christoph.fischer@elkw.de</a>
                </p>
            @endcomponent
            @if((null !== $blog) && (count($blog->channel->item)))
                @component('components.ui.card')
                    @slot('cardHeader')
                        Neuigkeiten / Infos zum Pfarrplaner
                    @endslot
                            @foreach ($blog->channel->item as $item)
                            <div class="row" style="margin-bottom: 1em;">
                                <div class="col-md-2">
                                    <a href="{{ $item->link }}" target="_blank">
                                        @if (isset($item->enclosure) && ((string)$item->enclosure['type'] == 'image/jpeg'))
                                            <img class="img-fluid" src="{{ (string)$item->enclosure['url'] }}" />
                                        @endif
                                    </a>
                                </div>
                                <div class="col-md-10">
                                    <span>{!! (new \Carbon\Carbon($item->pubDate))->formatLocalized('%A, %d. %B %Y') !!}</span>
                                    <a href="{{ $item->link }}" target="_blank"><h2>{{ $item->title }}</h2></a>
                                    <p>{!! $item->description !!}</p>
                                </div>
                            </div>
                            @endforeach
                @endcomponent
            @endif
    @endcomponent
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#email_input').focus();
        });
    </script>
@endsection
