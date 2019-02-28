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
                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                    <span class="fa fa-th-list"></span> Sammeleingabe <span class="caret"></span>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                    @foreach(\App\Inputs\Inputs::all() as $input)
                    <a class="dropdown-item" href="{{ route('inputs.setup', $input->getKey()) }}">
                        {{ $input->title }}
                    </a>
                    @endforeach
                </div>
            </li>
        @endif
        @if (Auth::user()->isAdmin || Auth::user()->canEditChurch)
            <li class="nav-item">
                <a class="nav-link" href="{{ route('whatsnew') }}">Neue Funktionen</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('connectWithOutlook') }}">Mit Outlook verbinden</a>
            </li>
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                <span class="fa fa-wrench"></span> Admin <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                @if (Auth::user()->isAdmin)
                <a class="dropdown-item" href="{{ route('users.index') }}">
                    Benutzer
                </a>
                <a class="dropdown-item" href="{{ route('cities.index') }}">
                    Kirchengemeinden
                </a>
                @endif
                @if (Auth::user()->canEditChurch)
                <a class="dropdown-item" href="{{ route('locations.index') }}">
                    Kirchen
                </a>
                @endif
            </div>
        </li>
        @endif
        <li class="nav-item dropdown">
            <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button"
               data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                <span class="fa fa-user"></span> {{ Auth::user()->name }} <span class="caret"></span>
            </a>

            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="{{ route('changePassword') }}">
                    Passwort ändern
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
