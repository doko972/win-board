<x-app-layout>
    <x-slot name="header">
        <div style="display:flex; align-items:center; gap:16px;">
            <a href="{{ route('dashboard') }}" class="btn btn--outline" style="padding:8px 12px;">←</a>
            <div>
                <h1 class="page-header__title">Modifier le RDV</h1>
                <p class="page-header__subtitle">{{ $appointment->client_name }}</p>
            </div>
        </div>
    </x-slot>

    <div style="max-width: 640px; margin: 0 auto;">
        <form method="POST" action="{{ route('appointments.update', $appointment) }}" class="form">
            @csrf
            @method('PATCH')

            {{-- Infos RDV --}}
            <div class="form-card">
                <div class="form-card__title">Informations du RDV</div>

                <div class="form" style="gap:16px;">
                    <div class="form__group">
                        <x-input-label for="title" value="Titre du RDV" />
                        <x-text-input id="title" name="title" type="text"
                            :value="old('title', $appointment->title)" required />
                        <x-input-error :messages="$errors->get('title')" />
                    </div>

                    <div class="form__group">
                        <x-input-label for="client_name" value="Nom du client / entreprise" />
                        <x-text-input id="client_name" name="client_name" type="text"
                            :value="old('client_name', $appointment->client_name)" required />
                        <x-input-error :messages="$errors->get('client_name')" />
                    </div>

                    <div class="form__group">
                        <x-input-label for="description" value="Description (optionnel)" />
                        <textarea id="description" name="description"
                            class="input" rows="3">{{ old('description', $appointment->description) }}</textarea>
                        <x-input-error :messages="$errors->get('description')" />
                    </div>

                    <div class="form__group">
                        <x-input-label for="scheduled_at" value="Date & heure du RDV" />
                        <x-text-input id="scheduled_at" name="scheduled_at" type="datetime-local"
                            :value="old('scheduled_at', $appointment->scheduled_at->format('Y-m-d\TH:i'))" required />
                        <x-input-error :messages="$errors->get('scheduled_at')" />
                    </div>
                </div>
            </div>

            {{-- Niveau --}}
            <div class="form-card">
                <div class="form-card__title">Niveau du RDV</div>
                <x-input-error :messages="$errors->get('level')" />
                @php $currentLevel = old('level', $appointment->level); @endphp
                <div class="level-cards" id="levelCards">
                    <label class="level-card level-card--bronze {{ $currentLevel === 'bronze' ? 'is-selected' : '' }}"
                           onclick="selectLevel('bronze')">
                        <input type="radio" name="level" value="bronze" {{ $currentLevel === 'bronze' ? 'checked' : '' }}>
                        <div class="level-card__inner">
                            <div class="level-card__emoji">🥉</div>
                            <div class="level-card__name">Découverte</div>
                            <div class="level-card__desc">Petit compte</div>
                            <div class="level-card__pts">+10 <span>pts</span></div>
                        </div>
                    </label>

                    <label class="level-card level-card--silver {{ $currentLevel === 'silver' ? 'is-selected' : '' }}"
                           onclick="selectLevel('silver')">
                        <input type="radio" name="level" value="silver" {{ $currentLevel === 'silver' ? 'checked' : '' }}>
                        <div class="level-card__inner">
                            <div class="level-card__emoji">🥈</div>
                            <div class="level-card__name">Qualifié</div>
                            <div class="level-card__desc">Compte moyen</div>
                            <div class="level-card__pts">+20 <span>pts</span></div>
                        </div>
                    </label>

                    <label class="level-card level-card--gold {{ $currentLevel === 'gold' ? 'is-selected' : '' }}"
                           onclick="selectLevel('gold')">
                        <input type="radio" name="level" value="gold" {{ $currentLevel === 'gold' ? 'checked' : '' }}>
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
                💾 Enregistrer les modifications
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
