<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:center; gap:16px;">
            <a href="{{ route('dashboard') }}" class="btn btn--outline" style="padding:8px 12px;">←</a>
            <div>
                <h1 class="page-header__title">Déclarer un RDV</h1>
                <p class="page-header__subtitle">Choisis le niveau et gagne des points 🚀</p>
            </div>
        </div>
    </x-slot>

    <div style="max-width: 640px; margin: 0 auto;">
        <form method="POST" action="{{ route('appointments.store') }}" class="form">
            @csrf

            {{-- Infos RDV --}}
            <div class="form-card">
                <div class="form-card__title">Informations du RDV</div>

                <div class="form" style="gap:16px;">
                    <div class="form__group">
                        <x-input-label for="title" value="Titre du RDV" />
                        <x-text-input id="title" name="title" type="text"
                            placeholder="Ex: Présentation offre fibre Pro"
                            :value="old('title')" required />
                        <x-input-error :messages="$errors->get('title')" />
                    </div>

                    <div class="form__group">
                        <x-input-label for="client_name" value="Nom du client / entreprise" />
                        <x-text-input id="client_name" name="client_name" type="text"
                            placeholder="Ex: Société Dupont SARL"
                            :value="old('client_name')" required />
                        <x-input-error :messages="$errors->get('client_name')" />
                    </div>

                    <div class="form__group">
                        <x-input-label for="description" value="Description (optionnel)" />
                        <textarea id="description" name="description"
                            class="input"
                            rows="3"
                            placeholder="Ex: Intéressé par l'offre fibre Pro, budget ~150€/mois, décideur présent...">{{ old('description') }}</textarea>
                        <x-input-error :messages="$errors->get('description')" />
                    </div>

                    <div class="form__group">
                        <x-input-label for="scheduled_at" value="Date & heure du RDV" />
                        <x-text-input id="scheduled_at" name="scheduled_at" type="datetime-local"
                            :value="old('scheduled_at')" required />
                        <x-input-error :messages="$errors->get('scheduled_at')" />
                    </div>
                </div>
            </div>

            {{-- Niveau --}}
            <div class="form-card">
                <div class="form-card__title">Niveau du RDV</div>
                <x-input-error :messages="$errors->get('level')" />
                <div class="level-cards" id="levelCards">
                    <label class="level-card level-card--bronze {{ old('level') === 'bronze' ? 'is-selected' : '' }}"
                           onclick="selectLevel('bronze')">
                        <input type="radio" name="level" value="bronze" {{ old('level') === 'bronze' ? 'checked' : '' }}>
                        <div class="level-card__inner">
                            <div class="level-card__emoji">🥉</div>
                            <div class="level-card__name">Découverte</div>
                            <div class="level-card__desc">Petit compte</div>
                            <div class="level-card__pts">+10 <span>pts</span></div>
                        </div>
                    </label>

                    <label class="level-card level-card--silver {{ old('level') === 'silver' ? 'is-selected' : '' }}"
                           onclick="selectLevel('silver')">
                        <input type="radio" name="level" value="silver" {{ old('level') === 'silver' ? 'checked' : '' }}>
                        <div class="level-card__inner">
                            <div class="level-card__emoji">🥈</div>
                            <div class="level-card__name">Qualifié</div>
                            <div class="level-card__desc">Compte moyen</div>
                            <div class="level-card__pts">+20 <span>pts</span></div>
                        </div>
                    </label>

                    <label class="level-card level-card--gold {{ old('level') === 'gold' ? 'is-selected' : '' }}"
                           onclick="selectLevel('gold')">
                        <input type="radio" name="level" value="gold" {{ old('level') === 'gold' ? 'checked' : '' }}>
                        <div class="level-card__inner">
                            <div class="level-card__emoji">🥇</div>
                            <div class="level-card__name">Confirmé</div>
                            <div class="level-card__desc">Grand compte</div>
                            <div class="level-card__pts">+30 <span>pts</span></div>
                        </div>
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn--primary btn--block">
                🎯 Valider le RDV
            </button>
        </form>
    </div>

    <script>
    function selectLevel(level) {
        document.querySelectorAll('.level-card').forEach(card => card.classList.remove('is-selected'));
        document.querySelector('.level-card--' + level).classList.add('is-selected');
    }
    </script>
</x-app-layout>
