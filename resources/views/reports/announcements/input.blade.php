@extends('layouts.app')

@section('title', 'Bekanntgaben erstellen')

@section('content')
    <form method="post" action="{{ route('reports.render', $report) }}">
        <div class="card">
            <div class="card-body">
                @csrf
                <input type="hidden" name="service" value="{{ $service->id }}"/>
                <div class="form-group">
                    <label class="control-label" for="lastService">Herzlichen Dank für das Opfer der Gottesdienste
                        vom...</label>
                    <select class="form-control" name="lastService">
                        @foreach ($lastDaysWithServices as $day)
                            <option value="{{ $day->date->format('d.m.Y') }}"
                                    data-offering="{{ $offerings[$day->id] }}">{{ $day->date->formatLocalized('%A, %d.%m.%Y') }}</option>
                        @endforeach
                    </select>
                    @component('components.validation', ['name' => 'lastService']) @endcomponent
                </div>
                <div class="form-group">
                    <label class="control-label" for="offerings">...in Höhe von ... €.</label>
                    <input class="form-control" type="text" id="offerings" name="offerings" value=""
                           placeholder="123,45"/>
                </div>
                <div class="form-group">
                    <label class="control-label" for="offering_text">Text zum Opfer</label>
                    <textarea class="form-control" type="text" name="offering_text"
                              placeholder="Wenn vorhanden, z.B. Anschreiben des Landesbischofs"></textarea>
                    @component('components.validation', ['name' => 'offering_text']) @endcomponent
                </div>
                @checkbox(['name' => 'mix_outlook', 'label' => 'Veranstaltungen aus dem Outlook-Kalender mit aufnehmen.', 'value' => true]) @endcheckbox
                @checkbox(['name' => 'mix_op', 'label' => 'Veranstaltungen aus dem Online-Planer mit aufnehmen.', 'value' => false]) @endcheckbox
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Erstellen</button>
            </div>
        </div>
    </form>
@endsection


@section('scripts')
    <script>
        function updateAmount() {
            $('#offerings').val($('select[name=lastService]').children('option:selected').data('offering'));
        }

        $(document).ready(function () {
            updateAmount();
            $('select[name=lastService]').change(updateAmount);
        });
    </script>
@endsection
