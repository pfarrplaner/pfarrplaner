@input(['name' => $tab->configKey('title'), 'label' => 'Titel', 'value' => $config['title']])
@cityselect(['name' => $tab->configKey('includeCities').'[]', 'label' => 'Kasualien für folgende Gemeinden anzeigen', 'multiple' => true, 'value' => $config['includeCities'], 'cities' => Auth::user()->writableCities])
