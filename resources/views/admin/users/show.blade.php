<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('admin.users.index') }}" class="page-header__back">← Utilisateurs</a>
        <h1 class="page-header__title">{{ $user->name }}</h1>
        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn--outline" style="margin-left:auto;">✏️ Modifier</a>
    </x-slot>

    @include('admin.partials.subnav')

    <div class="admin">

        <div class="admin__grid" style="margin-bottom:24px;">

            {{-- Infos profil --}}
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">👤 Profil</h2>
                </div>
                <div class="admin-profile">
                    <div class="admin-leader__avatar" style="width:56px;height:56px;font-size:1.25rem;">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div>
                        <div style="font-size:1.125rem;font-weight:700;color:#f9fafb;">{{ $user->name }}</div>
                        <div style="font-size:.875rem;color:#6b7280;margin-top:2px;">{{ $user->email }}</div>
                        <div style="margin-top:8px;">
                            <span class="admin-badge admin-badge--{{ $user->role }}">
                                {{ $user->role === 'admin' ? '🔑 Admin' : '💼 Commercial' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Stats --}}
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">📊 Statistiques</h2>
                </div>
                <div class="admin__stats" style="grid-template-columns:repeat(3,1fr);">
                    <div class="admin-stat">
                        <div class="admin-stat__value">{{ $user->points }}</div>
                        <div class="admin-stat__label">Points</div>
                    </div>
                    <div class="admin-stat">
                        <div class="admin-stat__value">{{ $user->appointments_count }}</div>
                        <div class="admin-stat__label">RDV total</div>
                    </div>
                    <div class="admin-stat">
                        <div class="admin-stat__value">{{ $user->badges->count() }}</div>
                        <div class="admin-stat__label">Badges</div>
                    </div>
                </div>
                <div style="display:flex;gap:8px;flex-wrap:wrap;padding:0 4px 8px;">
                    @foreach(['gold' => ['Confirmé','#fbbf24'], 'silver' => ['Qualifié','#9ca3af'], 'bronze' => ['Découverte','#d97706']] as $lvl => [$label, $color])
                    <div style="background:rgba(255,255,255,.04);border-radius:8px;padding:8px 14px;text-align:center;flex:1;">
                        <div style="font-size:1.25rem;font-weight:900;color:{{ $color }};">
                            {{ $statsByLevel[$lvl]->count ?? 0 }}
                        </div>
                        <div style="font-size:.625rem;color:#4b5563;text-transform:uppercase;letter-spacing:.06em;margin-top:2px;">{{ $label }}</div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Badges obtenus --}}
        @if($user->badges->count())
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title">🏅 Badges obtenus</h2>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:12px;padding:8px 4px 12px;">
                @foreach($user->badges as $badge)
                <div style="display:flex;align-items:center;gap:8px;background:rgba(255,255,255,.04);border-radius:10px;padding:10px 14px;">
                    <span style="font-size:1.5rem;">{{ $badge->icon }}</span>
                    <div>
                        <div style="font-size:.875rem;font-weight:700;color:#e5e7eb;">{{ $badge->name }}</div>
                        <div style="font-size:.75rem;color:#4b5563;">
                            {{ \Carbon\Carbon::parse($badge->pivot->obtained_at)->format('d/m/Y') }}
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Historique des RDV --}}
        <div class="card">
            <div class="card__header">
                <h2 class="card__title">📋 Historique des rendez-vous</h2>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Client</th>
                        <th>Niveau</th>
                        <th>Points</th>
                        <th>Date RDV</th>
                        <th>Déclaré le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td>{{ $appt->title }}</td>
                        <td>{{ $appt->client_name }}</td>
                        <td><span class="admin-badge admin-badge--{{ $appt->level }}">
                            {{ $appt->level === 'gold' ? 'Confirmé' : ($appt->level === 'silver' ? 'Qualifié' : 'Découverte') }}
                        </span></td>
                        <td>+{{ $appt->points_value }}</td>
                        <td>{{ $appt->scheduled_at->format('d/m/Y') }}</td>
                        <td class="admin-table__muted">{{ $appt->created_at->format('d/m H\hi') }}</td>
                        <td><a href="{{ route('admin.appointments.edit', $appt) }}" class="admin-table__action">Modifier</a></td>
                    </tr>
                    @empty
                    <tr><td colspan="7" style="text-align:center;color:#4b5563;padding:24px;">Aucun rendez-vous.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($appointments->hasPages())
                <div class="admin__pagination">{{ $appointments->links() }}</div>
            @endif
        </div>

    </div>
</x-app-layout>
