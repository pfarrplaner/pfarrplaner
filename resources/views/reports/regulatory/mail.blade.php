@component('mail::layout')
    {{-- Header --}}
    @slot('header')
        @component('mail::header', ['url' => null])
            {{ $sender }}
        @endcomponent
    @endslot

    {{-- Body --}}
    {!! $text !!}

    {{-- Subcopy --}}
    @isset($subcopy)
        @slot('subcopy')
            @component('mail::subcopy')
                {{ $subcopy }}
            @endcomponent
        @endslot
    @endisset

    {{-- Footer --}}
    @slot('footer')
        @component('mail::footer')
            Diese Nachricht wurde automatisch über www.pfarrplaner.de gesendet. <br />
            Bei Rückfragen wenden Sie sich bitte direkt an: <br />
            {{ $user->fullName(true) }}@if($user->phone), Tel. ({{ $user->phone }}@endif, E-Mail {{ $user->email }}.
        @endcomponent
    @endslot
@endcomponent



