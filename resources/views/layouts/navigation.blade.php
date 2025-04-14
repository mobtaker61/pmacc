<nav class="navbar navbar-expand-lg navbar-dark bg-dark border-bottom">
    <div class="container">
        <a href="{{ route('dashboard') }}" class="navbar-brand d-flex align-items-center">
            <img src="{{ asset('images/PM-Proje-icon-w.png') }}" alt="{{ config('app.name') }}" width="40" height="40" class="d-inline-block align-text-top me-2">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-home me-1"></i>
                        {{ __('all.app.dashboard') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('petty-cash.transactions.index') }}" class="nav-link {{ request()->routeIs('petty-cash.transactions.*') ? 'active' : '' }}">
                        <i class="fas fa-exchange-alt me-1"></i>
                        {{ __('all.petty_cash.navigation.petty_cash_transactions') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('parties.index') }}" class="nav-link {{ request()->routeIs('parties.*') ? 'active' : '' }}">
                        <i class="fas fa-users me-1"></i>
                        {{ __('parties.parties') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('expenses.index') }}" class="nav-link {{ request()->routeIs('expenses.*') ? 'active' : '' }}">
                        <i class="fas fa-money-bill me-1"></i>
                        {{ __('expenses.expenses') }}
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                        <i class="fas fa-cog me-1"></i>
                        {{ __('all.settings.title') }}
                    </a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-globe me-1"></i>
                        {{ strtoupper(isset($app_locale) ? $app_locale : app()->getLocale()) }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('switch-to-fa').submit();">
                                <span class="flag-icon">🇮🇷</span> فارسی
                            </a>
                            <form action="{{ route('language.switch', 'fa') }}" method="POST" id="switch-to-fa" style="display: none;">
                                @csrf
                            </form>
                        </li>
                        <li>
                            <a href="#" class="dropdown-item" onclick="event.preventDefault(); document.getElementById('switch-to-tr').submit();">
                                <span class="flag-icon">🇹🇷</span> Türkçe
                            </a>
                            <form action="{{ route('language.switch', 'tr') }}" method="POST" id="switch-to-tr" style="display: none;">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-user-circle me-1"></i>
                        {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                <i class="fas fa-user me-2"></i> {{ __('all.app.profile') }}
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt me-2"></i> {{ __('all.app.logout') }}
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
