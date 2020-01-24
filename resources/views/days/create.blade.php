@extends('layouts.app')

@section('title', 'Tag hinzufügen')

@section('content')
    <form method="post" action="{{ route('days.store') }}">
        @csrf
        @component('components.ui.card')
            @slot('cardFooter')
                <button type="submit" class="btn btn-primary" id="submit">Hinzufügen</button>
            @endslot
                <div class="form-group">
                <label for="date">Datum</label>
                <input type="text" class="form-control datepicker" name="date" placeholder="tt.mm.jjjj"
                       value="01.{{ str_pad($month, 2, 0, STR_PAD_LEFT) }}.{{ $year }}"/>
            </div>
            <div class="form-group">
                <label for="name">Bezeichnung des Tages</label>
                <input type="text" class="form-control" name="name"
                       placeholder="leer lassen für automatischen Eintrag"/>
            </div>
            <div class="form-group">
                <label style="display:block;">Anzeige</label>
                <div class="form-check">
                    <input type="radio" name="day_type" value="{{ \App\Day::DAY_TYPE_DEFAULT }}"
                           autocomplete="off" id="check-type-default">
                    <label class="form-check-label">
                        Diesen Tag für alle Gemeinden anzeigen
                    </label>
                </div>
                <div class="form-check">
                    <input type="radio" name="day_type" value="{{ \App\Day::DAY_TYPE_LIMITED }}"
                           autocomplete="off" id="check-type-limited" checked>
                    <label class="form-check-label">
                        Diesen Tag nur für folgende Gemeinden anzeigen:
                    </label>
                    @foreach ($cities as $city)
                        <div class="form-check">
                            <input class="form-check-input city-check @if(Auth::user()->cities->contains($city))my-city @else not-my-city @endif"
                                   type="checkbox" name="cities[]" value="{{ $city->id }}"
                                   id="defaultCheck{{$city->id}}">
                            <label class="form-check-label" for="defaultCheck{{$city->id}}">
                                {{$city->name}}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endcomponent
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('.city-check').change(function () {
                var limit = false;
                $('.city-check').each(function () {
                    limit = limit || $(this).prop('checked');
                });
                if (limit) {
                    $('#check-type-limited').prop('checked', true);
                    $('#check-type-default').prop('checked', false);
                } else {
                    $('#check-type-limited').prop('checked', false);
                    $('#check-type-default').prop('checked', true);
                }
            });
        });
    </script>
@endsection
