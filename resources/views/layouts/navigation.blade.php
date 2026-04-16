<nav class="navbar">
    <div class="navbar__left">
        <a href="{{ route('dashboard') }}" class="navbar__logo">HRTélécomsBoard</a>
        <div class="navbar__links">
            <a href="{{ route('dashboard') }}"
               class="navbar__link {{ request()->routeIs('dashboard') ? 'navbar__link--active' : '' }}">
                🏆 Classement
            </a>
            @if(!auth()->user()->isAdmin())
            <a href="{{ route('appointments.create') }}"
               class="navbar__link {{ request()->routeIs('appointments.create') ? 'navbar__link--active' : '' }}">
                🎯 Déclarer un RDV
            </a>
            @endif
            @if(auth()->user()->isAdmin())
            <a href="{{ route('admin.dashboard') }}"
               class="navbar__link navbar__link--admin {{ request()->routeIs('admin.*') ? 'navbar__link--active' : '' }}">
                ⚙️ Administration
            </a>
            @endif
        </div>
    </div>

    <div class="navbar__right">
        <div class="navbar__points">
            <span class="navbar__points-value">{{ auth()->user()->points ?? 0 }}</span>
            <span class="navbar__points-label">pts</span>
        </div>

        <div class="user-menu" id="userMenu">
            <button class="user-menu__trigger" onclick="toggleUserMenu()">
                <div class="user-menu__avatar">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <span class="user-menu__name">{{ auth()->user()->name }}</span>
                <svg class="user-menu__arrow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
            <div class="user-menu__dropdown" id="userDropdown">
                <a href="{{ route('profile.edit') }}" class="user-menu__item">⚙️ Mon profil</a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="user-menu__item user-menu__item--danger">🚪 Déconnexion</button>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleUserMenu() {
    document.getElementById('userDropdown').classList.toggle('is-open');
}
document.addEventListener('click', function(e) {
    if (!document.getElementById('userMenu').contains(e.target)) {
        document.getElementById('userDropdown').classList.remove('is-open');
    }
});
</script>
