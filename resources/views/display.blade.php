<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>HRTélécomsBoard — Grand Écran</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800,900&display=swap" rel="stylesheet" />
    @vite(['resources/scss/app.scss', 'resources/js/display.js'])
</head>
<body>
<div class="display" id="displayRoot" data-last-appt-id="{{ $latestApptId }}" data-poll-url="{{ route('display.latest') }}">

    {{-- ── Header ──────────────────────────────────────── --}}
    <header class="display__header">
        <div class="display__header-left">
            <div class="display__logo-wrap">
                <div id="lottie-logo" class="display__lottie"></div>
                <span class="display__logo">HRTélécomsBoard</span>
            </div>
            <span class="display__tagline">Classement en direct</span>
        </div>
        <div class="display__header-right">
            <span class="display__clock" id="clock"></span>
            <button class="display__sound-btn" id="soundBtn" onclick="enableAudio()">🔇 Activer le son</button>
            <a href="{{ route('login') }}" class="display__nav-link">Interface commerciaux →</a>
        </div>
    </header>

    {{-- ── Corps ───────────────────────────────────────── --}}
    <div class="display__body">

        {{-- Leaderboard --}}
        <div class="display__left">

            {{-- Podium --}}
            <div class="display__podium-wrap">
                @php $top3 = $leaderboard->take(3); @endphp
                <div class="podium">
                    @if($top3->count() >= 2)
                    <div class="podium__slot">
                        <div class="podium__crown"></div>
                        <div class="podium__avatar podium__avatar--2">{{ strtoupper(substr($top3[1]->name, 0, 2)) }}</div>
                        <div class="podium__name">{{ $top3[1]->name }}</div>
                        <div class="podium__pts">{{ $top3[1]->points }} pts</div>
                        <div class="podium__bar podium__bar--2">🥈</div>
                    </div>
                    @endif

                    <div class="podium__slot">
                        <div class="podium__crown">👑 Leader</div>
                        <div class="podium__avatar podium__avatar--1">{{ strtoupper(substr($top3[0]->name, 0, 2)) }}</div>
                        <div class="podium__name">{{ $top3[0]->name }}</div>
                        <div class="podium__pts podium__pts--gold">{{ $top3[0]->points }} pts</div>
                        <div class="podium__bar podium__bar--1">🥇</div>
                    </div>

                    @if($top3->count() >= 3)
                    <div class="podium__slot">
                        <div class="podium__crown"></div>
                        <div class="podium__avatar podium__avatar--3">{{ strtoupper(substr($top3[2]->name, 0, 2)) }}</div>
                        <div class="podium__name">{{ $top3[2]->name }}</div>
                        <div class="podium__pts">{{ $top3[2]->points }} pts</div>
                        <div class="podium__bar podium__bar--3">🥉</div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Liste complète --}}
            <div class="display__list" id="leaderboard-list">
                @php $maxPts = max($leaderboard->first()?->points ?? 1, 1); @endphp
                @foreach($leaderboard as $rank => $user)
                <div class="display__row" data-user-id="{{ $user->id }}">
                    <div class="display__rank display__rank--{{ $rank === 0 ? '1' : ($rank === 1 ? '2' : ($rank === 2 ? '3' : 'n')) }}">
                        {{ $rank + 1 }}
                    </div>
                    <div class="display__avatar {{ $rank === 0 ? 'display__avatar--gold' : '' }}">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="display__info">
                        <div class="display__name">{{ $user->name }}</div>
                        <div class="leaderboard__bar-wrap">
                            <div class="leaderboard__bar-fill {{ $rank === 0 ? 'leaderboard__bar-fill--gold' : '' }}"
                                 style="width: {{ $maxPts > 0 ? round($user->points / $maxPts * 100) : 0 }}%">
                            </div>
                        </div>
                        <div class="display__sub">
                            {{ $user->appointments_count }} RDV
                            @if($user->badges->count())
                                · {{ $user->badges->map(fn($b) => $b->icon)->join(' ') }}
                            @endif
                        </div>
                    </div>
                    <div class="display__score">
                        <div class="display__score-value">{{ $user->points }}</div>
                        <div class="display__score-label">pts</div>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- Feed derniers RDV --}}
            <div class="display__feed">
                <div class="display__feed-title">🕐 Derniers RDV</div>
                <div class="display__feed-list" id="feed-list">
                    @foreach($recentAppts as $appt)
                    <div class="display__feed-item display__feed-item--{{ $appt['level'] }}">
                        <div class="display__feed-level display__feed-level--{{ $appt['level'] }}">
                            {{ $appt['level'] === 'gold' ? '🥇' : ($appt['level'] === 'silver' ? '🥈' : '🥉') }}
                        </div>
                        <div class="display__feed-info">
                            <div class="display__feed-client">{{ $appt['client_name'] }}</div>
                            <div class="display__feed-meta">{{ $appt['user_name'] }} · {{ $appt['scheduled_at'] }}</div>
                        </div>
                        <div class="display__feed-pts">+{{ $appt['points_value'] }}</div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Google Calendar --}}
        <div class="display__center">
            @if(config('services.google.calendar_embed_url'))
                <iframe src="{{ config('services.google.calendar_embed_url') }}" frameborder="0" scrolling="no"></iframe>
            @else
                <div class="display__cal-placeholder">
                    <div class="display__cal-placeholder-icon">📅</div>
                    <h2 class="display__cal-placeholder-title">Google Calendar</h2>
                    <p class="display__cal-placeholder-desc">
                        Ajoutez l'URL d'intégration dans votre fichier <code>.env</code> pour afficher l'agenda partagé.
                    </p>
                    <div class="display__cal-instructions">
                        <p class="display__cal-instructions-title">Comment obtenir l'URL :</p>
                        <ol>
                            <li>Ouvre <span>Google Calendar</span></li>
                            <li>⚙️ Paramètres → sélectionne le calendrier partagé</li>
                            <li>Section <span>"Intégrer l'agenda"</span></li>
                            <li>Copie le <span>lien src de l'iframe</span></li>
                            <li>Ajoute dans <code>.env</code> :</li>
                        </ol>
                        <div class="display__cal-code">GOOGLE_CALENDAR_EMBED_URL=https://calendar.google.com/...</div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Colonne stats --}}
        <div class="display__stats" id="stats-col"
             data-bronze="{{ $stats['bronze'] }}"
             data-silver="{{ $stats['silver'] }}"
             data-gold="{{ $stats['gold'] }}">

            {{-- Camembert --}}
            <div class="dstat__block">
                <div class="dstat__title">Répartition RDV</div>
                <div class="dstat__pie-wrap">
                    <div class="dstat__pie" id="pie-chart"></div>
                    <div class="dstat__pie-legend">
                        <div class="dstat__pie-item dstat__pie-item--gold">
                            <span class="dstat__pie-dot"></span>
                            <span id="pct-gold">{{ $stats['gold_pct'] }}%</span> Confirmé
                        </div>
                        <div class="dstat__pie-item dstat__pie-item--silver">
                            <span class="dstat__pie-dot"></span>
                            <span id="pct-silver">{{ $stats['silver_pct'] }}%</span> Qualifié
                        </div>
                        <div class="dstat__pie-item dstat__pie-item--bronze">
                            <span class="dstat__pie-dot"></span>
                            <span id="pct-bronze">{{ $stats['bronze_pct'] }}%</span> Découverte
                        </div>
                    </div>
                </div>
            </div>

            {{-- Aujourd'hui --}}
            <div class="dstat__block">
                <div class="dstat__title">📅 Aujourd'hui</div>
                <div class="dstat__row">
                    <div class="dstat__kpi">
                        <div class="dstat__kpi-value" id="today-count">{{ $stats['today_count'] }}</div>
                        <div class="dstat__kpi-label">RDV</div>
                    </div>
                    <div class="dstat__kpi">
                        <div class="dstat__kpi-value dstat__kpi-value--brand" id="today-points">{{ $stats['today_points'] }}</div>
                        <div class="dstat__kpi-label">points</div>
                    </div>
                </div>
            </div>

            {{-- Semaine --}}
            <div class="dstat__block">
                <div class="dstat__title">📈 Cette semaine</div>
                <div class="dstat__row">
                    <div class="dstat__kpi">
                        <div class="dstat__kpi-value" id="week-count">{{ $stats['week_count'] }}</div>
                        <div class="dstat__kpi-label">RDV</div>
                    </div>
                    <div class="dstat__kpi">
                        <div class="dstat__kpi-value dstat__kpi-value--gold" id="gold-pct-kpi">{{ $stats['gold_pct'] }}%</div>
                        <div class="dstat__kpi-label">taux confirmé</div>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>

{{-- Overlay + Popup --}}
<div class="popup-overlay" id="popupOverlay" onclick="hidePopup()"></div>

<div class="popup" id="popup" style="display:none;">
    <div class="popup__emoji" id="popupEmoji">🎉</div>
    <div class="popup__tag">Nouveau RDV décroché !</div>
    <div class="popup__name" id="popupUser">–</div>
    <div class="popup__detail">vient de décrocher un RDV avec <strong id="popupClient"></strong></div>
    <div class="popup__badges">
        <span class="popup__level" id="popupLevel"></span>
        <span class="popup__points" id="popupPoints"></span>
    </div>
    <div class="popup__timer"><div class="popup__timer-fill" id="popupTimer"></div></div>
</div>

<div class="confetti-container" id="confettiContainer"></div>
</body>
</html>
