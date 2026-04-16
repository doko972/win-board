<x-app-layout>
    <x-slot name="header">
        <h1 class="page-header__title">Remises à zéro</h1>
    </x-slot>

    @include('admin.partials.subnav')

    <div class="admin">

        @if(session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif

        <div class="admin__grid">

            {{-- Formulaire reset --}}
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">🔄 Nouvelle remise à zéro</h2>
                </div>
                <p style="font-size:.875rem; color:#6b7280; margin-bottom:20px;">
                    Remet les points de tous les <strong style="color:#d1d5db;">{{ $userCount }} commerciaux</strong> à zéro
                    et archive le classement actuel. Les rendez-vous sont conservés.
                </p>
                <form method="POST" action="{{ route('admin.resets.store') }}" class="form"
                      onsubmit="return confirm('Confirmer la remise à zéro ? Cette action est irréversible.')">
                    @csrf
                    <div class="form__group">
                        <label class="form__label">Nom de la période terminée</label>
                        <input type="text" name="label" value="{{ old('label') }}"
                               class="input {{ $errors->has('label') ? 'input--invalid' : '' }}"
                               placeholder="ex : Avril 2026, T1 2026…" required>
                        @error('label')<div class="form__error">{{ $message }}</div>@enderror
                    </div>
                    <div class="form__group">
                        <label class="admin-checkbox">
                            <input type="checkbox" name="reset_badges" value="1" {{ old('reset_badges') ? 'checked' : '' }}>
                            <span>Réinitialiser aussi les badges des commerciaux</span>
                        </label>
                    </div>
                    <button type="submit" class="btn btn--danger btn--block">
                        🔄 Lancer la remise à zéro
                    </button>
                </form>
            </div>

            {{-- Info --}}
            <div class="card">
                <div class="card__header">
                    <h2 class="card__title">ℹ️ Comment ça fonctionne</h2>
                </div>
                <ul class="admin-info-list">
                    <li>✅ Le classement actuel est <strong>archivé</strong> avec les scores</li>
                    <li>✅ Les <strong>points</strong> de tous les commerciaux passent à 0</li>
                    <li>✅ Les <strong>rendez-vous</strong> sont conservés dans l'historique</li>
                    <li>⚠️ Option badges : retire tous les badges pour une nouvelle course</li>
                    <li>❌ Cette action <strong>ne peut pas être annulée</strong></li>
                </ul>
            </div>

        </div>

        {{-- Historique --}}
        @if($resets->count())
        <div class="card" style="margin-top:24px;">
            <div class="card__header">
                <h2 class="card__title">📜 Historique des périodes</h2>
            </div>
            @foreach($resets as $reset)
            <div class="admin-reset-block">
                <div class="admin-reset-block__header">
                    <div>
                        <span class="admin-reset-block__label">{{ $reset->label }}</span>
                        @if($reset->badges_reset)
                            <span class="admin-badge" style="margin-left:8px;">badges réinitialisés</span>
                        @endif
                    </div>
                    <span class="admin-reset-block__meta">
                        Par {{ $reset->admin->name }} · {{ $reset->created_at->format('d/m/Y à H\hi') }}
                    </span>
                </div>
                <table class="admin-table" style="margin-top:8px;">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Commercial</th>
                            <th>Points</th>
                            <th>RDV</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reset->snapshot as $row)
                        <tr>
                            <td>
                                @if($row['rank'] === 1) 🥇
                                @elseif($row['rank'] === 2) 🥈
                                @elseif($row['rank'] === 3) 🥉
                                @else {{ $row['rank'] }}
                                @endif
                            </td>
                            <td>{{ $row['name'] }}</td>
                            <td><strong>{{ $row['points'] }}</strong></td>
                            <td>{{ $row['appointments_count'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
        @endif

    </div>
</x-app-layout>
