<x-app-layout>
    <x-slot name="header">
        <h1 class="page-header__title">Rendez-vous</h1>
        <a href="{{ route('admin.appointments.export', request()->only('user_id','level','date_from','date_to')) }}"
           class="btn btn--outline">⬇️ Export CSV</a>
    </x-slot>

    @include('admin.partials.subnav')

    <div class="admin">

        @if(session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif

        {{-- Filtres --}}
        <div class="card" style="margin-bottom:20px;">
            <form method="GET" action="{{ route('admin.appointments.index') }}" class="admin-filters">
                <div class="form__group">
                    <label class="form__label">Commercial</label>
                    <select name="user_id" class="input">
                        <option value="">Tous</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form__group">
                    <label class="form__label">Niveau</label>
                    <select name="level" class="input">
                        <option value="">Tous</option>
                        <option value="bronze" {{ request('level') === 'bronze' ? 'selected' : '' }}>🥉 Découverte</option>
                        <option value="silver" {{ request('level') === 'silver' ? 'selected' : '' }}>🥈 Qualifié</option>
                        <option value="gold"   {{ request('level') === 'gold'   ? 'selected' : '' }}>🥇 Confirmé</option>
                    </select>
                </div>
                <div class="form__group">
                    <label class="form__label">Du</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}" class="input">
                </div>
                <div class="form__group">
                    <label class="form__label">Au</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}" class="input">
                </div>
                <div class="form__group" style="justify-content:flex-end;">
                    <button type="submit" class="btn btn--primary">Filtrer</button>
                    @if(request()->hasAny(['user_id','level','date_from','date_to']))
                        <a href="{{ route('admin.appointments.index') }}" class="btn btn--outline">Réinitialiser</a>
                    @endif
                </div>
            </form>
        </div>

        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Commercial</th>
                        <th>Titre</th>
                        <th>Client</th>
                        <th>Niveau</th>
                        <th>Points</th>
                        <th>Date RDV</th>
                        <th>Déclaré le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($appointments as $appt)
                    <tr>
                        <td>{{ $appt->user->name }}</td>
                        <td>{{ $appt->title }}</td>
                        <td>{{ $appt->client_name }}</td>
                        <td><span class="admin-badge admin-badge--{{ $appt->level }}">
                            {{ $appt->level === 'gold' ? 'Confirmé' : ($appt->level === 'silver' ? 'Qualifié' : 'Découverte') }}
                        </span></td>
                        <td>+{{ $appt->points_value }}</td>
                        <td>{{ $appt->scheduled_at->format('d/m/Y') }}</td>
                        <td class="admin-table__muted">{{ $appt->created_at->format('d/m H\hi') }}</td>
                        <td>
                            <div class="admin-table__actions">
                                <a href="{{ route('admin.appointments.edit', $appt) }}" class="admin-table__action">Modifier</a>
                                <form method="POST" action="{{ route('admin.appointments.destroy', $appt) }}"
                                      onsubmit="return confirm('Supprimer ce RDV ? Les points seront restitués.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="admin-table__action admin-table__action--danger">Supprimer</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="8" style="text-align:center;color:#4b5563;padding:24px;">Aucun résultat.</td></tr>
                    @endforelse
                </tbody>
            </table>
            @if($appointments->hasPages())
                <div class="admin__pagination">{{ $appointments->links() }}</div>
            @endif
        </div>

    </div>
</x-app-layout>
