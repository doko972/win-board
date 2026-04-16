<x-app-layout>
    <x-slot name="header">
        <h1 class="page-header__title">Badges</h1>
    </x-slot>

    @include('admin.partials.subnav')

    <div class="admin">

        @if(session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert--danger">{{ session('error') }}</div>
        @endif

        {{-- Attribution manuelle --}}
        <div class="card" style="margin-bottom:24px;">
            <div class="card__header">
                <h2 class="card__title">🎁 Attribuer un badge manuellement</h2>
            </div>
            <form method="POST" action="{{ route('admin.badges.award') }}" class="admin-award-form">
                @csrf
                <div class="form__group">
                    <label class="form__label">Commercial</label>
                    <select name="user_id" class="input" required>
                        <option value="">— Choisir —</option>
                        @foreach($commerciaux as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form__group">
                    <label class="form__label">Badge</label>
                    <select name="badge_id" class="input" required>
                        <option value="">— Choisir —</option>
                        @foreach($badges as $badge)
                            <option value="{{ $badge->id }}">{{ $badge->icon }} {{ $badge->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn--primary">Attribuer</button>
            </form>
        </div>

        {{-- Liste des badges --}}
        @foreach($badges as $badge)
        <div class="card" style="margin-bottom:16px;">
            <div class="card__header">
                <div class="admin-badge-header">
                    <span class="admin-badge-header__icon">{{ $badge->icon }}</span>
                    <div>
                        <div class="admin-badge-header__name">{{ $badge->name }}</div>
                        <div class="admin-badge-header__desc">{{ $badge->description }}</div>
                    </div>
                    <span class="admin-badge" style="margin-left:auto;">
                        {{ $badge->users->count() }} {{ Str::plural('titulaire', $badge->users->count()) }}
                    </span>
                </div>
            </div>

            @if($badge->users->count())
            <div class="admin-badge-holders">
                @foreach($badge->users as $holder)
                <div class="admin-badge-holder">
                    <div class="admin-user__avatar" style="width:28px;height:28px;font-size:.6rem;">
                        {{ strtoupper(substr($holder->name, 0, 2)) }}
                    </div>
                    <span>{{ $holder->name }}</span>
                    <span class="admin-table__muted" style="font-size:.75rem;">
                        {{ \Carbon\Carbon::parse($holder->pivot->obtained_at)->format('d/m/Y') }}
                    </span>
                    <form method="POST" action="{{ route('admin.badges.revoke') }}" style="margin-left:auto;">
                        @csrf
                        <input type="hidden" name="user_id" value="{{ $holder->id }}">
                        <input type="hidden" name="badge_id" value="{{ $badge->id }}">
                        <button type="submit" class="admin-table__action admin-table__action--danger"
                                onclick="return confirm('Retirer ce badge ?')">Retirer</button>
                    </form>
                </div>
                @endforeach
            </div>
            @else
                <p style="font-size:.875rem;color:#4b5563;padding:12px 16px;">Aucun titulaire pour l'instant.</p>
            @endif
        </div>
        @endforeach

    </div>
</x-app-layout>
