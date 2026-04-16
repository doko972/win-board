<nav class="admin-subnav">
    <a href="{{ route('admin.dashboard') }}"
       class="admin-subnav__link {{ request()->routeIs('admin.dashboard') ? 'admin-subnav__link--active' : '' }}">
        📊 Tableau de bord
    </a>
    <a href="{{ route('admin.users.index') }}"
       class="admin-subnav__link {{ request()->routeIs('admin.users.*') ? 'admin-subnav__link--active' : '' }}">
        👥 Utilisateurs
    </a>
    <a href="{{ route('admin.appointments.index') }}"
       class="admin-subnav__link {{ request()->routeIs('admin.appointments.*') ? 'admin-subnav__link--active' : '' }}">
        📋 Rendez-vous
    </a>
    <a href="{{ route('admin.badges.index') }}"
       class="admin-subnav__link {{ request()->routeIs('admin.badges.*') ? 'admin-subnav__link--active' : '' }}">
        🏅 Badges
    </a>
    <a href="{{ route('admin.resets.index') }}"
       class="admin-subnav__link {{ request()->routeIs('admin.resets.*') ? 'admin-subnav__link--active' : '' }}">
        🔄 Remises à zéro
    </a>
</nav>
