@extends('layouts.app')

@section('title', 'Tag bearbeiten')

@section('content')
    <div class="container">
        <div class="card mt-5">
            <div class="card-header">
                Tag bearbeiten
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
                <form id="frmEdit" method="post" action="{{ route('days.update', $day->id) }}">
                    @method('PATCH')
                    @csrf
                    <div class="form-group">
                        <label for="date">Datum</label>
                        <input type="text" class="form-control datepicker" name="date" placeholder="tt.mm.jjjj"
                               value="{{ $day->date->format('d.m.Y') }}" @if (!Auth::user()->isAdmin) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label for="name">Bezeichnung des Tages</label>
                        <input type="text" class="form-control" name="name" value="{{ $day->name }}"  placeholder="leer lassen für automatischen Eintrag" @if (!Auth::user()->isAdmin) disabled @endif/>
                    </div>
                    <div class="form-group">
                        <label style="display:block;">Anzeige</label>
                        <div class="form-check">
                            <input type="radio" name="day_type" value="{{ \App\Day::DAY_TYPE_DEFAULT }}"
                                   autocomplete="off" @if($day->day_type == \App\Day::DAY_TYPE_DEFAULT) checked @endif>
                            <label class="form-check-label">
                                Diesen Tag für alle Gemeinden anzeigen
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="day_type" value="{{ \App\Day::DAY_TYPE_LIMITED }}"
                                   autocomplete="off" id="check-type-limited" @if($day->day_type == \App\Day::DAY_TYPE_LIMITED) checked @endif>
                            <label class="form-check-label">
                                Diesen Tag nur für folgende Gemeinden anzeigen:
                            </label>
                            @foreach ($cities as $city)
                                <div class="form-check">
                                    <input class="form-check-input city-check @if(Auth::user()->cities->contains($city))my-city @else not-my-city @endif" type="checkbox" name="cities[]" value="{{ $city->id }}"
                                           id="defaultCheck{{$city->id}}" @if($day->cities->contains($city)) checked @endif>
                                    <label class="form-check-label" for="defaultCheck{{$city->id}}">
                                        {{$city->name}}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" id="btnSave">Speichern</button>
                </form>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(){
            function updateCityChecks() {
                if ($('#check-type-limited').prop('checked')) {
                    $('.city-check.my-city').attr('disabled', false);
                } else {
                    $('.city-check').attr('disabled', true);
                }
            }
            $('.datepicker').datepicker({
                language: 'de',
                format: 'dd.mm.yyyy',
                startDate: '{{ $day->date->format('Y-m-d') }}',
            });

            updateCityChecks();
            $('input[name=day_type]').on('change', function(){
                updateCityChecks();
            });
            $('#btnSave').click(function(event){
                event.preventDefault();
                $('input[name=date]').attr('disabled', false);
                $('input[name=name]').attr('disabled', false);
                $('.city-check').attr('disabled', false);
                $('#frmEdit').submit();
            });
        });
    </script>
@endsection
