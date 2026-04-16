<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('admin.appointments.index') }}" class="page-header__back">← Rendez-vous</a>
        <h1 class="page-header__title">Modifier le RDV</h1>
    </x-slot>

    @include('admin.partials.subnav')

    <div class="admin">
        <div class="form-card" style="max-width: 560px;">
            <form method="POST" action="{{ route('admin.appointments.update', $appointment) }}" class="form">
                @csrf @method('PATCH')

                <div class="form__group">
                    <label class="form__label">Commercial</label>
                    <select name="user_id" class="input">
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ $appointment->user_id === $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form__group">
                    <label class="form__label">Titre du RDV</label>
                    <input type="text" name="title" value="{{ old('title', $appointment->title) }}"
                           class="input {{ $errors->has('title') ? 'input--invalid' : '' }}" required>
                    @error('title')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group">
                    <label class="form__label">Nom du client</label>
                    <input type="text" name="client_name" value="{{ old('client_name', $appointment->client_name) }}"
                           class="input {{ $errors->has('client_name') ? 'input--invalid' : '' }}" required>
                    @error('client_name')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group">
                    <label class="form__label">Niveau</label>
                    <select name="level" class="input">
                        <option value="bronze" {{ old('level', $appointment->level) === 'bronze' ? 'selected' : '' }}>🥉 Découverte (+10 pts)</option>
                        <option value="silver" {{ old('level', $appointment->level) === 'silver' ? 'selected' : '' }}>🥈 Qualifié (+20 pts)</option>
                        <option value="gold"   {{ old('level', $appointment->level) === 'gold'   ? 'selected' : '' }}>🥇 Confirmé (+30 pts)</option>
                    </select>
                </div>

                <div class="form__group">
                    <label class="form__label">Date du RDV</label>
                    <input type="datetime-local" name="scheduled_at"
                           value="{{ old('scheduled_at', $appointment->scheduled_at->format('Y-m-d\TH:i')) }}"
                           class="input {{ $errors->has('scheduled_at') ? 'input--invalid' : '' }}" required>
                    @error('scheduled_at')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group" style="flex-direction: row; gap: 12px; margin-top: 8px;">
                    <button type="submit" class="btn btn--primary btn--block">Enregistrer</button>
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn--outline btn--block">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
