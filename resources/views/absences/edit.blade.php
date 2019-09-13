@extends('layouts.app')

@section('title', 'Urlaub bearbeiten')

@section('content')
    @component('components.container')
        <div class="card">
            <div class="card-header">
                Urlaub bearbeiten
            </div>
            <div class="card-body">
                @component('components.errors')
                @endcomponent
                <form method="post" action="{{ route('absences.update', $absence->id) }}">
                    @csrf
                    @method('PATCH')
                    <div class="form-group">
                        <label for="reason">Daten:</label>
                        <input type="hidden" name="month" value="{{ $month }}" />
                        <input type="hidden" name="year" value="{{ $year }}" />
                        <input type="hidden" id="from" name="from" value="{{ $absence->from->format('d.m.Y') }}"/>
                        <input type="hidden" id="to" name="to" value="{{ $absence->to->format('d.m.Y') }}"/>
                        <div id="date-range12"></div>
                        <div id="date-range12-container"></div>
                    </div>
                    <div class="form-group">
                        <label for="reason">Beschreibung:</label>
                        <input type="text" class="form-control" name="reason" value="{{ $absence->reason }}"/>
                    </div>
                    <div class="form-group">
                        <label for="replacement"><span class="fa fa-user"></span>&nbsp;Vertretung</label>
                        <select class="form-control peopleSelect" name="replacement" multiple placeholder="Eine oder mehrere Personen (keine Anmerkungen!)" >
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" @if($absence->replacement()->first()->id == $user->id) selected @endif>{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <hr />
                    <button type="submit" class="btn btn-primary">Speichern</button>
                </form>
            </div>
        </div>
        <hr />
        <form class="form-inline" style="display: inline;" action="{{ route('absences.destroy', $absence->id)}}" method="post">
            @csrf
            @method('DELETE')
            <input type="hidden" name="month" value="{{ $month }}" />
            <input type="hidden" name="year" value="{{ $year }}" />
            <button class="btn btn-danger" type="submit" title="Urlaubseintrag löschen"><span class="fa fa-trash"></span> Urlaubseintrag löschen</button>
        </form>
        <script>
            $(function() {

                $('.peopleSelect').select2({
                    placeholder: 'Eine oder mehrere Personen (keine Anmerkungen!)',
                    allowClear: true,
                    multiple: true,
                    allowclear: true,
                    tags: true,
                    createTag: function (params) {
                        return {
                            id: params.term,
                            text: params.term,
                            newOption: true
                        }
                    },
                    templateResult: function (data) {
                        var $result = $("<span></span>");

                        $result.text(data.text);

                        if (data.newOption) {
                            $result.append(" <em>(Neue Person anlegen)</em>");
                        }

                        return $result;
                    },
                });
                // css fix for select2 when tabbing to anything else than home (why???)
                $('.select2-container--default').css('width', '1068px');


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