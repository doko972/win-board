<x-app-layout>
    <x-slot name="header">
        <div>
            <h1 class="page-header__title">Tableau de bord</h1>
            <p class="page-header__subtitle">Bon courage {{ auth()->user()->name }} 💪</p>
        </div>
        <a href="{{ route('appointments.create') }}" class="btn btn--primary">
            🎯 Déclarer un RDV
        </a>
    </x-slot>

    <div class="section-gap">

        {{-- Succès --}}
        @if(session('success'))
            <div class="alert alert--success anim-bounce-in">
                <span class="alert__icon">🎉</span>
                {{ session('success') }}
            </div>
        @endif

        {{-- Stats --}}
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-box__value stat-box__value--brand">{{ auth()->user()->points }}</div>
                <div class="stat-box__label">Points</div>
            </div>
            <div class="stat-box">
                <div class="stat-box__value stat-box__value--purple">{{ auth()->user()->appointments()->count() }}</div>
                <div class="stat-box__label">RDV décrochés</div>
            </div>
            <div class="stat-box">
                <div class="stat-box__value stat-box__value--gold">{{ auth()->user()->badges()->count() }}</div>
                <div class="stat-box__label">Badges</div>
            </div>
        </div>

        {{-- Podium --}}
        @if($leaderboard->count() >= 1)
        <div class="card">
            <div class="card__header"><h2>🏆 Podium</h2></div>
            <div class="podium">
                @if($leaderboard->count() >= 2)
                <div class="podium__slot">
                    <div class="podium__crown"></div>
                    <div class="podium__avatar podium__avatar--2">{{ strtoupper(substr($leaderboard[1]->name, 0, 2)) }}</div>
                    <div class="podium__name">{{ $leaderboard[1]->name }}</div>
                    <div class="podium__pts">{{ $leaderboard[1]->points }} pts</div>
                    <div class="podium__bar podium__bar--2">🥈</div>
                </div>
                @endif

                <div class="podium__slot">
                    <div class="podium__crown">👑 Leader</div>
                    <div class="podium__avatar podium__avatar--1">{{ strtoupper(substr($leaderboard[0]->name, 0, 2)) }}</div>
                    <div class="podium__name">{{ $leaderboard[0]->name }}</div>
                    <div class="podium__pts podium__pts--gold">{{ $leaderboard[0]->points }} pts</div>
                    <div class="podium__bar podium__bar--1">🥇</div>
                </div>

                @if($leaderboard->count() >= 3)
                <div class="podium__slot">
                    <div class="podium__crown"></div>
                    <div class="podium__avatar podium__avatar--3">{{ strtoupper(substr($leaderboard[2]->name, 0, 2)) }}</div>
                    <div class="podium__name">{{ $leaderboard[2]->name }}</div>
                    <div class="podium__pts">{{ $leaderboard[2]->points }} pts</div>
                    <div class="podium__bar podium__bar--3">🥉</div>
                </div>
                @endif
            </div>
        </div>
        @endif

        <div class="grid-2">
            {{-- Classement --}}
            <div class="card">
                <div class="card__header"><h2>Classement général</h2></div>
                <div class="leaderboard__list">
                    @forelse($leaderboard as $rank => $user)
                        <div class="leaderboard__row {{ $user->id === auth()->id() ? 'leaderboard__row--me' : '' }}">
                            <div class="leaderboard__rank leaderboard__rank--{{ $rank === 0 ? '1' : ($rank === 1 ? '2' : ($rank === 2 ? '3' : 'other')) }}">
                                {{ $rank + 1 }}
                            </div>
                            <div class="leaderboard__avatar {{ $rank === 0 ? 'leaderboard__avatar--gold' : '' }}">
                                {{ strtoupper(substr($user->name, 0, 2)) }}
                            </div>
                            <div class="leaderboard__info">
                                <div class="leaderboard__name">
                                    {{ $user->name }}
                                    @if($user->id === auth()->id())
                                        <span class="leaderboard__name-tag">← toi</span>
                                    @endif
                                </div>
                                <div class="leaderboard__sub">
                                    {{ $user->appointments_count }} RDV
                                    @if($user->badges->count())
                                        · {{ $user->badges->map(fn($b) => $b->icon)->join(' ') }}
                                    @endif
                                </div>
                            </div>
                            <div class="leaderboard__pts">
                                <div class="leaderboard__pts-value">{{ $user->points }}</div>
                                <div class="leaderboard__pts-label">pts</div>
                            </div>
                        </div>
                    @empty
                        <div class="card__empty">
                            <div class="card__empty-text">Aucun commercial pour l'instant</div>
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Derniers RDV --}}
            <div class="card">
                <div class="card__header">
                    <h2>Mes derniers RDV</h2>
                    <a href="{{ route('appointments.create') }}" class="card__link">+ Nouveau</a>
                </div>
                <div class="appt-list">
                    @forelse($myAppointments as $appt)
                        <div class="appt-row">
                            <div class="appt-row__icon">
                                @if($appt->level === 'gold') 🥇
                                @elseif($appt->level === 'silver') 🥈
                                @else 🥉
                                @endif
                            </div>
                            <div class="appt-row__info">
                                <div class="appt-row__title">{{ $appt->title }}</div>
                                <div class="appt-row__client">{{ $appt->client_name }}</div>
                                <div class="appt-row__date">{{ $appt->scheduled_at->format('d/m/Y à H:i') }}</div>
                            </div>
                            <div class="appt-row__pts">
                                <div class="appt-row__pts-value appt-row__pts-value--{{ $appt->level }}">
                                    +{{ $appt->points_value }}
                                </div>
                                <div class="appt-row__pts-label">pts</div>
                            </div>
                            <a href="{{ route('appointments.edit', $appt) }}" class="appt-row__edit">✏️</a>
                        </div>
                    @empty
                        <div class="card__empty">
                            <div class="card__empty-icon">🎯</div>
                            <div class="card__empty-text">Aucun RDV pour l'instant</div>
                            <a href="{{ route('appointments.create') }}" class="card__empty-link">Déclare ton premier RDV →</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Badges --}}
        @if(auth()->user()->badges->count())
            <div class="card">
                <div class="card__header"><h2>🎖️ Mes badges</h2></div>
                <div class="badge-list">
                    @foreach(auth()->user()->badges as $badge)
                        <div class="badge-pill">
                            <span class="badge-pill__icon">{{ $badge->icon }}</span>
                            <div>
                                <div class="badge-pill__name">{{ $badge->name }}</div>
                                <div class="badge-pill__date">{{ \Carbon\Carbon::parse($badge->pivot->obtained_at)->format('d/m/Y') }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

    </div>
</x-app-layout>
