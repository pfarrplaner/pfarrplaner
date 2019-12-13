@extends('layouts.app')

@section('title') Mehrere Gottesdienste anlegen @endsection

@section('content')
    <form method="post" action="{{ route('inputs.input', $input->getKey()) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <div class="form-group"> <!-- Radio group !-->
                    <label class="control-label">Mehrere Gottesdienste für folgende Kirchen anlegen:</label>
                    @foreach($locations as $location)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="includeLocations[]"
                                   value="{{ $location->id }}"
                                   id="defaultCheck{{$location->id}}"/>
                            <label class="form-check-label" for="defaultCheck{{$location->id}}">
                                {{$location->name}}
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="form-group">
                    <input type="hidden" id="from" name="from" value=""/>
                    <input type="hidden" id="to" name="to" value=""/>
                    <div id="date-range12"></div>
                    <div id="date-range12-container"></div>
                </div>
                <div class="form-group">
                    <label for="rhythm">Rhythmus</label><br/>
                    Jede <input type="text" class="form-control" style="width: 50px; display: inline;" name="rhythm"
                                value="1"/>. Woche
                </div>
                <div class="form-group">
                    <label for="weekday">Wochentag</label><br/>
                    <select class="form-control" name="weekday">
                        <option value="Sunday" selected>Sonntag</option>
                        <option value="Monday">Montag</option>
                        <option value="Tuesday">Dienstag</option>
                        <option value="Wednesday">Mittwoch</option>
                        <option value="Thursday">Donnerstag</option>
                        <option value="Friday">Freitag</option>
                        <option value="Saturday">Samstag</option>
                    </select>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Anlegen</button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        $(document).ready(function () {
            $('#date-range12').dateRangePicker({
                separator: ' - ',
                format: 'DD.MM.Y',
                inline: true,
                container: '#date-range12-container',
                alwaysOpen: true,
                language: 'de',
                stickyMonths: true,
                getValue: function () {
                    if ($('#from').val() && $('#to').val())
                        return $('#from').val() + ' - ' + $('#to').val();
                    else
                        return '';
                },
                setValue: function (s, s1, s2) {
                    $('#from').val(s1);
                    $('#to').val(s2);
                }
            });
        });
    </script>
@endsection
