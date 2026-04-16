<x-app-layout>
    <x-slot name="header">
        <h1 class="page-header__title">Administration</h1>
    </x-slot>

    @include('admin.partials.subnav')

    <div class="admin">

        @if(session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif

        {{-- Stats globales --}}
        <div class="admin__stats">
            <div class="admin-stat">
                <div class="admin-stat__value">{{ $stats['users_total'] }}</div>
                <div class="admin-stat__label">Commerciaux</div>
            </div>
            <div class="admin-stat">
                <div class="admin-stat__value">{{ $stats['appts_total'] }}</div>
                <div class="admin-stat__label">RDV total</div>
            </div>
            <div class="admin-stat">
                <div class="admin-stat__value">{{ $stats['appts_today'] }}</div>
                <div class="admin-stat__label">RDV aujourd'hui</div>
            </div>
            <div class="admin-stat">
                <div class="admin-stat__value">{{ $stats['appts_week'] }}</div>
                <div class="admin-stat__label">RDV cette semaine</div>
            </div>
            <div class="admin-stat">
                <div class="admin-stat__value">{{ $stats['points_distributed'] }}</div>
                <div class="admin-stat__label">Points distribués</div>
            </div>
        </div>

        <div class="admin__grid">

            {{-- Meilleur commercial --}}
            @if($topUser)
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">🏆 Leader actuel</h2>
                </div>
                <div class="admin-leader">
                    <div class="admin-leader__avatar">{{ strtoupper(substr($topUser->name, 0, 2)) }}</div>
                    <div class="admin-leader__info">
                        <div class="admin-leader__name">{{ $topUser->name }}</div>
                        <div class="admin-leader__pts">{{ $topUser->points }} points</div>
                    </div>
                </div>
            </div>
            @endif

            {{-- URL kiosque --}}
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">📺 URL Kiosque (Raspberry Pi)</h2>
                </div>
                @php $kioskUrl = url('/tv/' . config('services.display_token')); @endphp
                <div class="admin-kiosk">
                    <div class="admin-kiosk__url" id="kioskUrl">{{ $kioskUrl }}</div>
                    <button class="btn btn--outline admin-kiosk__copy" onclick="copyKioskUrl()">
                        📋 Copier
                    </button>
                </div>
                <p class="admin-kiosk__hint">
                    Configurez cette URL dans le navigateur du Raspberry Pi en mode kiosque.
                </p>
                <script>
                function copyKioskUrl() {
                    navigator.clipboard.writeText('{{ $kioskUrl }}').then(() => {
                        const btn = document.querySelector('.admin-kiosk__copy');
                        btn.textContent = '✅ Copié !';
                        setTimeout(() => btn.textContent = '📋 Copier', 2000);
                    });
                }
                </script>
            </div>

        </div>

        {{-- Derniers RDV --}}
        <div class="card">
            <div class="card__header">
                <h2 class="card__title">🕐 Derniers rendez-vous</h2>
                <a href="{{ route('admin.appointments.index') }}" class="card__link">Voir tout →</a>
            </div>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Commercial</th>
                        <th>Client</th>
                        <th>Niveau</th>
                        <th>Points</th>
                        <th>Date RDV</th>
                        <th>Déclaré le</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentAppts as $appt)
                    <tr>
                        <td>{{ $appt->user->name }}</td>
                        <td>{{ $appt->client_name }}</td>
                        <td><span class="admin-badge admin-badge--{{ $appt->level }}">
                            {{ $appt->level === 'gold' ? 'Confirmé' : ($appt->level === 'silver' ? 'Qualifié' : 'Découverte') }}
                        </span></td>
                        <td>+{{ $appt->points_value }}</td>
                        <td>{{ $appt->scheduled_at->format('d/m/Y') }}</td>
                        <td>{{ $appt->created_at->format('d/m H\hi') }}</td>
                        <td><a href="{{ route('admin.appointments.edit', $appt) }}" class="admin-table__action">Modifier</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
