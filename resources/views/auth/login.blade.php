<x-guest-layout>
    <h2 class="auth-card__title">Connexion</h2>

    <x-auth-session-status class="auth-status" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="form">
        @csrf

        <div class="form__group">
            <x-input-label for="email" value="Adresse e-mail" />
            <x-text-input id="email" name="email" type="email"
                :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" />
        </div>

        <div class="form__group">
            <x-input-label for="password" value="Mot de passe" />
            <x-text-input id="password" name="password" type="password"
                required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" />
        </div>

        <div class="checkbox-group">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Se souvenir de moi</label>
        </div>

        <button type="submit" class="btn btn--primary btn--block">Se connecter</button>

        <div class="auth-card__links">
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
            @endif
            @if (Route::has('register'))
                <a href="{{ route('register') }}">Créer un compte</a>
            @endif
        </div>
    </form>
</x-guest-layout>
