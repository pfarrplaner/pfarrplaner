<div class="mb-1">
    <a class="btn btn-primary" href="{{ route('calendar', ['year' => null, 'month' => null, 'slave' => 1]) }}"
       target="_blank"
       title="Öffnet ein weiteres Fenster mit einer Kalenderansicht, die der hier dargestellten automatisch folgt.">
        <span class="fa fa-desktop"></span> 2. Bildschirm anzeigen
    </a>
</div>
<hr class="mb-2">
<h6>Anordnung</h6>
<div class="mb-1">
    <input name="orientation" value="horizontal" type="radio" @if($orientation=='horizontal')checked @endif> Horizontale
    Ansicht <br/>
    <small>(Tage als Spalten)</small>
    <br/>
    <input name="orientation" value="vertical" type="radio" @if($orientation=='vertical')checked @endif> Vertikale
    Ansicht <br/>
    <small>(Tage als Zeilen)</small>
    <br/>
</div>
<hr class="mb-2">
<div class="mb-1">
    <h6>Reihenfolge</h6>
    <input type="hidden" name="citySort" value=""/>
    <ul id="userCities" class="sortable citySort">
        @foreach($sortedCities as $city)
            <li data-city="{{ $city->id }}"><span
                        class="fa fa-church"></span> {{ $city->name }}</li>
        @endforeach
    </ul>
    Nicht anzeigen:<br/>
    <ul id="unusedCities" class="sortable citySort">
        @foreach($unusedCities as $city)
            <li data-city="{{ $city->id }}"><span
                        class="fa fa-church"></span> {{ $city->name }}</li>
        @endforeach
    </ul>
    <a id="applySorting" class="btn btn-secondary btn-sm" href="{{ route('calendar', ['year' => $year, 'month' => $month, 'sort' => $sortedCities->pluck('id')->implode(',') ]) }}">Anwenden</a>
</div>
<hr class="mb-2">
<div class="mb-1">
    <h6>Namen anzeigen als:</h6>
    <?php $nameFormat = Auth::user()->getSetting('calendar_name_format'); ?>
    <select name="name_sort" id="ctrlNameSort" class="form-control" data-route="{{ route('calendar', ['year' => $year, 'month' => $month ]) }}">
        <option @if($nameFormat == \App\Http\Controllers\CalendarController::NAME_FORMAT_DEFAULT)selected @endif value="{{ \App\Http\Controllers\CalendarController::NAME_FORMAT_DEFAULT }}">Pfr. Müller</option>
        <option @if($nameFormat == \App\Http\Controllers\CalendarController::NAME_FORMAT_INITIAL_AND_LAST)selected @endif value="{{ \App\Http\Controllers\CalendarController::NAME_FORMAT_INITIAL_AND_LAST }}">Pfr. K. Müller</option>
        <option @if($nameFormat == \App\Http\Controllers\CalendarController::NAME_FORMAT_FIRST_AND_LAST)selected @endif value="{{ \App\Http\Controllers\CalendarController::NAME_FORMAT_FIRST_AND_LAST }}">Pfr. Karl Müller</option>
    </select>
</div>
