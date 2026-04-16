<x-app-layout>
    <x-slot name="header">
        <a href="{{ route('admin.users.index') }}" class="page-header__back">← Utilisateurs</a>
        <h1 class="page-header__title">Nouvel utilisateur</h1>
    </x-slot>

    @include('admin.partials.subnav')

    <div class="admin">
        <div class="form-card" style="max-width: 560px;">
            <form method="POST" action="{{ route('admin.users.store') }}" class="form">
                @csrf

                <div class="form__group">
                    <label class="form__label">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="input {{ $errors->has('name') ? 'input--invalid' : '' }}"
                           placeholder="Jean Dupont" required>
                    @error('name')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group">
                    <label class="form__label">Adresse email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                           class="input {{ $errors->has('email') ? 'input--invalid' : '' }}"
                           placeholder="jean@exemple.fr" required>
                    @error('email')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group">
                    <label class="form__label">Mot de passe</label>
                    <input type="password" name="password"
                           class="input {{ $errors->has('password') ? 'input--invalid' : '' }}"
                           placeholder="8 caractères minimum" required>
                    @error('password')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group">
                    <label class="form__label">Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation"
                           class="input" placeholder="Répétez le mot de passe" required>
                </div>

                <div class="form__group">
                    <label class="form__label">Rôle</label>
                    <select name="role" class="input {{ $errors->has('role') ? 'input--invalid' : '' }}">
                        <option value="commercial" {{ old('role', 'commercial') === 'commercial' ? 'selected' : '' }}>
                            💼 Commercial
                        </option>
                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                            🔑 Administrateur
                        </option>
                    </select>
                    @error('role')<div class="form__error">{{ $message }}</div>@enderror
                </div>

                <div class="form__group" style="flex-direction: row; gap: 12px; margin-top: 8px;">
                    <button type="submit" class="btn btn--primary btn--block">Créer l'utilisateur</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn--outline btn--block">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
