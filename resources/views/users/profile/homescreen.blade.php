@if(null !== $homeScreen)
    @tab(['id' => 'homescreen', 'active' => $tab == 'homescreen'])
    <h3>Inhalte des Startbildschirms</h3>
    <p>Die folgenden Reiter werden auf deinem Startbildschirm angezeigt. Um sie zu sortieren, kannst du sie ziehen und an der passenden Stelle ablegen.</p>
    <input type="hidden" name="homeScreenTabs" value="{{ join(',',$activeTabs) }}">
    <div id="activeHomeScreenTabs" class="profile-homescreen-accordion" role="tablist">
        @foreach ($homeScreenTabsActive as $homeScreenTab)
            <div class="card" data-key="{{ $homeScreenTab->getKey() }}">
                <div class="card-header" role="tab" id="heading{{ $loop->index }}">
                    <h5 class="mb-0">
                        <a data-toggle="collapse" href="#collapse{{ $loop->index }}" aria-expanded="true" aria-controls="collapse{{ $loop->index }}">
                            <i class="fa fa-arrows" aria-hidden="true"></i> {{ $homeScreenTab->getTitle() }}
                        </a>
                    </h5>
                </div>

                <div id="collapse{{ $loop->index }}" class="collapse" role="tabpanel" aria-labelledby="heading{{ $loop->index }}" data-parent="#activeHomeScreenTabs">
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
            <div class="card-header" role="tab" id="heading{{ $loop->index }}">
                <h5 class="mb-0">
                    <a data-toggle="collapse" href="#collapse{{ $loop->index }}" aria-expanded="true" aria-controls="collapse{{ $loop->index }}">
                        <i class="fa fa-arrows" aria-hidden="true"></i> {{ $homeScreenTab->getTitle() }}
                    </a>
                </h5>
            </div>

            <div id="collapse{{ $loop->index }}" class="collapse" role="tabpanel" aria-labelledby="heading{{ $loop->index }}" data-parent="#activeHomeScreenTabs">
                <div class="card-body">
                    {{ $homeScreenTab->getDescription() }}
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @endtab
@endif
