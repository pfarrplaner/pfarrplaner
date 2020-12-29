<!-- Right Side Of Navbar -->
<ul class="navbar-nav ml-auto">
    <!-- Authentication Links -->
    @guest
        <li class="nav-item">
            <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
        </li>
    @else
        @if (\App\Inputs\Inputs::all())
            <li class="nav-item dropdown">
                <a id="navbarDropdown1" class="nav-link dropdown-toggle" href="#" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                    <span class="fa fa-th-list"></span> Sammeleingabe <span class="caret"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown1">
                    @foreach(\App\Inputs\Inputs::all() as $input)
                    <a class="dropdown-item" href="{{ route('inputs.setup', $input->getKey()) }}">
                        {{ $input->title }}
                    </a>
                    @endforeach
                </div>
            </li>
        @endif
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whatsnew') }}"><span class="fa fa-sun" title="Neue Funktionen"></span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('ical.connect') }}"><span class="fa fa-calendar-alt" title="Mit Outlook verbinden"></span></a>
            </li>
        @canany(['benutzerliste-lokal-sehen', 'benutzer-bearbeiten', 'ort-bearbeiten', 'kirche-bearbeiten', 'rollen-bearbeiten'])
        <li class="nav-item dropdown">
            <a id="navbarDropdown2" class="nav-link dropdown-toggle" href="#" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                <span class="fa fa-wrench"></span> Admin <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown2">
                @canany(['benutzerliste-lokal-sehen', 'benutzer-bearbeiten'])
                <a class="dropdown-item" href="{{ route('users.index') }}">
                    Benutzer
                </a>
                @endcan
                @can('rollen-bearbeiten')
                    <a class="dropdown-item" href="{{ route('roles.index') }}">
                        Benutzerrollen
                    </a>
                @endcan
                @canany(['ort-bearbeiten', 'gd-opfer-bearbeiten'])
                <a class="dropdown-item" href="{{ route('cities.index') }}">
                    Kirchengemeinden
                </a>
                @endcan
                @can('kirche-bearbeiten')
                <a class="dropdown-item" href="{{ route('locations.index') }}">
                    Kirchen / Gottesdienstorte
                </a>
                @endcan
                @can('tags-bearbeiten')
                    <a class="dropdown-item" href="{{ route('tags.index') }}">
                        Kennzeichnungen
                    </a>
                @endcan
                @can('pfarraemter-bearbeiten')
                    <a class="dropdown-item" href="{{ route('parishes.index') }}">
                        Pfarrämter
                    </a>
                @endcan
                @canany(['benutzerliste-lokal-sehen', 'benutzer-bearbeiten', 'rollen-bearbeiten', 'ort-bearbeiten', 'kirche-bearbeiten', 'tags-bearbeiten'])
                    <div class="dropdown-divider"></div>
                @endcanany
                    <a class="dropdown-item" href="{{ route('calendar', ['year' => null, 'month' => null, 'slave' => 1]) }}" target="_blank">
                        Automatische Kalenderansicht öffnen
                    </a>
            </div>
        </li>
        @endcanany
        <li class="nav-item dropdown">
            <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" >
                <span class="fa fa-user"></span> {{ Auth::user()->name }} <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown3">
                <a class="dropdown-item" href="{{ route('password.edit') }}">
                    Passwort ändern
                </a>
                <a class="dropdown-item" href="{{ route('user.profile') }}">
                    Mein Profil
                </a>
                <a class="dropdown-item" href="{{ route('logout') }}"
                   onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                    {{ __('Logout') }}
                </a>

                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                      style="display: none;">
                    @csrf
                </form>
            </div>
        </li>
    @endguest
</ul>
