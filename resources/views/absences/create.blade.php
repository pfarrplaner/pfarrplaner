@extends('layouts.app')

@section('title', 'Urlaub anlegen')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Urlaub anlegen
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('absences.store') }}">
                    @csrf
                    <div class="form-group">
                        <label for="reason">Daten:</label>
                        <input type="hidden" name="month" value="{{ $month }}" />
                        <input type="hidden" name="year" value="{{ $year }}" />
                        <input type="hidden" name="user_id" value="{{$user->id}}" />
                        <input type="hidden" id="from" name="from" value=""/>
                        <input type="hidden" id="to" name="to" value=""/>
                        <div id="date-range12"></div>
                        <div id="date-range12-container"></div>
                    </div>
                    <div class="form-group">
                        <label for="reason">Beschreibung:</label>
                        <input type="text" class="form-control" name="reason" value=""/>
                    </div>
                    <div class="form-group">
                        <label for="replacement"><span class="fa fa-user"></span>&nbsp;Vertretung</label>
                        <select class="form-control peopleSelect" name="replacement" multiple placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
        <script>
            $(function() {

                $('.peopleSelect').selectize({
                    create: true,
                    render: {
                        option_create: function (data, escape) {
                            return '<div class="create">Neue Person anlegen: <strong>' + escape(data.input) + '</strong>&hellip;</div>';
                        }
                    },
                });

                $('#date-range12').dateRangePicker({
                    separator : ' - ',
                    format: 'DD.MM.Y',
                    inline:true,
                    container: '#date-range12-container',
                    alwaysOpen:true,
                    language: 'de',
                    stickyMonths: true,
                    getValue: function()
                    {
                        if ($('#from').val() && $('#to').val() )
                            return $('#from').val() + ' - ' + $('#to').val();
                        else
                            return '';
                    },
                    setValue: function(s,s1,s2)
                    {
                        $('#from').val(s1);
                        $('#to').val(s2);
                    }
                });
            });
        </script>
    @endcomponent
@endsection