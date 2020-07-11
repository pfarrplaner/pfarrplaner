<?php $value = unserialize(Auth::user()->getSetting('homeScreen_ministries', '')) ?: []; ?>
<div class="form-group">
    <label for="ministries">Zeige mir alle Gottesdienste mit leeren Feldern für folgende Dienste:</label>
    <select id="selectMinistry" class="form-control fancy-selectize" name="homeScreen[ministries][]" multiple>
        <option value="P" @if(in_array('P', $value)) selected @endif>Pfarrer*in</option>
        <option value="O" @if(in_array('O', $value)) selected @endif>Organist*in</option>
        <option value="M" @if(in_array('M', $value)) selected @endif>Mesner*in</option>
        <option value="A" @if(in_array('A', $value)) selected @endif>Weitere Beteiligte</option>
        @foreach (\App\Ministry::all() as $ministry)
            <option value="{{ $ministry }}" @if(in_array($ministry, $value)) selected @endif>{{ $ministry }}</option>
        @endforeach
    </select>
</div>
