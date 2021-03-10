@if(null !== $homeScreen)
    @tab(['id' => 'homescreen', 'active' => $tab == 'homescreen'])
    <h3>Inhalte des Startbildschirms</h3>
    @checkbox(['label' => 'Schaltflächen für das schnelle Erstellen von Kasualien anzeigen', 'name' => 'settings[homeScreenConfig][wizardButtons]', 'value' => \Illuminate\Support\Facades\Auth::user()->getSetting('homeScreenConfig', ['wizardButtons' => 0])['wizardButtons']])
    <p>Die folgenden Reiter werden auf deinem Startbildschirm angezeigt. Um sie zu sortieren, kannst du sie ziehen und an der passenden Stelle ablegen.</p>
    <input type="hidden" name="homeScreenTabs" value="{{ join(',',$activeTabs) }}">
    <div id="activeHomeScreenTabs" class="profile-homescreen-accordion" role="tablist">
        @foreach ($homeScreenTabsActive as $homeScreenTab)
            <div class="card" data-key="{{ $homeScreenTab->getKey() }}">
                <div class="card-header" role="tab" id="heading{{ ucfirst($homeScreenTab->getKey()) }}">
                    <h5 class="mb-0">
                        <a data-toggle="collapse" href="#collapse{{ ucfirst($homeScreenTab->getKey()) }}" aria-expanded="true" aria-controls="collapse{{ ucfirst($homeScreenTab->getKey()) }}">
                            <i class="fa fa-arrows" aria-hidden="true"></i> {{ $homeScreenTab->getTitle() }}
                        </a>
                    </h5>
                </div>

                <div id="collapse{{ ucfirst($homeScreenTab->getKey()) }}" class="collapse" role="tabpanel" aria-labelledby="heading{{ ucfirst($homeScreenTab->getKey()) }}" data-parent="#activeHomeScreenTabs">
                    <div class="card-body">
                        {{ $homeScreenTab->getDescription() }}<hr />
                        {!! $homeScreenTab->view('config', ['config' => ($homeScreenTabsConfig[$homeScreenTab->getKey()] ?? [])]) !!}
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    <h3 style="margin-top: 1em;">Deaktivierte Elemente</h3>
    <p>Die folgenden Reiter werden auf deinem Startbildschirm nicht angezeigt. Um sie zu aktivieren, ziehe sie einfach in die Liste oben.</p>
    <div class="profile-homescreen-accordion" role="tablist">
        @foreach ($homeScreenTabsInactive as $homeScreenTab)
        <div class="card" data-key="{{ $homeScreenTab->getKey() }}">
            <div class="card-header" role="tab" id="heading{{ ucfirst($homeScreenTab->getKey()) }}">
                <h5 class="mb-0">
                    <a data-toggle="collapse" href="#collapse{{ ucfirst($homeScreenTab->getKey()) }}" aria-expanded="true" aria-controls="collapse{{ ucfirst($homeScreenTab->getKey()) }}">
                        <i class="fa fa-arrows" aria-hidden="true"></i> {{ $homeScreenTab->getTitle() }}
                    </a>
                </h5>
            </div>

            <div id="collapse{{ ucfirst($homeScreenTab->getKey()) }}" class="collapse" role="tabpanel" aria-labelledby="heading{{ ucfirst($homeScreenTab->getKey()) }}" data-parent="#activeHomeScreenTabs">
                <div class="card-body">
                    {{ $homeScreenTab->getDescription() }}<hr />
                    {!! $homeScreenTab->view('config', ['config' => ($homeScreenTabsConfig[$homeScreenTab->getKey()] ?? [])]) !!}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @endtab
@endif
