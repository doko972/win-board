<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('admin.users.index') }}" class="page-header__back">← Utilisateurs</a>
        <h1 class="page-header__title">Modifier — {{ $user->name }}</h1>
    </x-slot>

    @include('admin.partials.subnav')

    <div class="admin">
        <div class="form-card" style="max-width: 560px;">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="form">
                @csrf @method('PATCH')

                @if(session('success'))
                    <div class="alert alert--success">{{ session('success') }}</div>
                @endif

                <div class="form__group">
                    <label class="form__label">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}"
                           class="input {{ $errors->has('name') ? 'input--invalid' : '' }}" required>
                    @error('name')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group">
                    <label class="form__label">Adresse email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}"
                           class="input {{ $errors->has('email') ? 'input--invalid' : '' }}" required>
                    @error('email')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group">
                    <label class="form__label">Rôle</label>
                    <select name="role" class="input">
                        <option value="commercial" {{ old('role', $user->role) === 'commercial' ? 'selected' : '' }}>
                            💼 Commercial
                        </option>
                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                            🔑 Administrateur
                        </option>
                    </select>
                </div>

                <div class="form__group">
                    <label class="form__label">Points</label>
                    <input type="number" name="points" value="{{ old('points', $user->points) }}"
                           class="input {{ $errors->has('points') ? 'input--invalid' : '' }}"
                           min="0" required>
                    @error('points')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group">
                    <label class="form__label">Nouveau mot de passe <span style="color:var(--text-600, #4b5563); font-weight:400;">(laisser vide pour ne pas changer)</span></label>
                    <input type="password" name="password"
                           class="input {{ $errors->has('password') ? 'input--invalid' : '' }}"
                           placeholder="8 caractères minimum">
                    @error('password')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group">
                    <label class="form__label">Confirmer le nouveau mot de passe</label>
                    <input type="password" name="password_confirmation" class="input">
                </div>

                <div class="form__group" style="flex-direction: row; gap: 12px; margin-top: 8px;">
                    <button type="submit" class="btn btn--primary btn--block">Enregistrer</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn--outline btn--block">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
