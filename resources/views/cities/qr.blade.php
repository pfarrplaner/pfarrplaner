@extends('layouts.app', ['noNavBar' => 1])

@section('title')QR-Codes für Gottesdienste in {{ $city->name }}@endsection

@section('content')
    @if(count($services))
        <div id="accordion">
            @foreach ($services as $service)

                <div class="card">
                    <div class="card-header" id="heading{{ $loop->index }}">
                        <h5 class="mb-0">
                            <button class="btn btn-link" data-toggle="collapse" data-target="#collapse{{ $loop->index }}"
                                    aria-expanded="true" aria-controls="collapseOne">
                                {{ $service->timeText() }}: {{ $service->titleText(false) }} ({{ $service->locationTExt }})<br />
                            </button>
                        </h5>
                    </div>

                    <div id="collapse{{ $loop->index }}" class="collapse @if($loop->index == 0)show @endif" aria-labelledby="heading{{ $loop->index }}" data-parent="#accordion">
                        <div class="card-body">
                            {!! QrCode::size(500)->generate($service->konfiapp_event_qr); !!}<br />
                            <div class="text-center">{{ $service->konfiapp_event_qr }}<br />
                                @foreach ($types as $type)
                                    @if($type->id == $service->konfiapp_event_type)
                                        {{ $type->punktzahl }} {{ $type->punktzahl == 1 ? 'Punkt' : 'Punkte' }} in der Kategorie
                                        "{{ $type->name }}"
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>
    @else
        @component('components.ui.card')
            <p>Heute gibt es keine Gottesdienste mit QR-Codes in {{ $city->name }}.</p>
        @endcomponent
    @endif

@endsection

@section('scripts')
    <script>
        $(document).ready(function(){
            $('#accordion svg').attr('width', '100%').attr('height', '100%');
            $('aside').remove();
            $('nav, .content-wrapper').attr('style', 'margin-left: 0 !important');
        });

    </script>
@endsection
