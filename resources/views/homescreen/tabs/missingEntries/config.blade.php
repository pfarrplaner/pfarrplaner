<div class="form-group">
    <label for="ministries">Zeige mir alle Gottesdienste mit leeren Feldern für folgende Dienste:</label>
    <select id="selectMinistry" class="form-control fancy-selectize" name="{{ $tab->configKey('ministries', true) }}" multiple>
        <option value="P" @if(in_array('P', $config['ministries'])) selected @endif>Pfarrer*in</option>
        <option value="O" @if(in_array('O', $config['ministries'])) selected @endif>Organist*in</option>
        <option value="M" @if(in_array('M', $config['ministries'])) selected @endif>Mesner*in</option>
        <option value="A" @if(in_array('A', $config['ministries'])) selected @endif>Weitere Beteiligte</option>
        @foreach (\App\Ministry::all() as $ministry)
            <option value="{{ $ministry }}" @if(in_array($ministry, $config['ministries'])) selected @endif>{{ $ministry }}</option>
        @endforeach
    </select>
    @locationselect(['name' => $tab->configKey('locations', true), 'label' => 'Auf folgende Orte beschränken', 'locations' => $locations, 'value' => $config['locations']])
</div>
