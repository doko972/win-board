<x-app-layout>
    <x-slot name="header">
        <h1 class="page-header__title">Utilisateurs</h1>
        <a href="{{ route('admin.users.create') }}" class="btn btn--primary">➕ Nouvel utilisateur</a>
    </x-slot>

    @include('admin.partials.subnav')

    <div class="admin">

        @if(session('success'))
            <div class="alert alert--success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert--danger">{{ session('error') }}</div>
        @endif

        <div class="card">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th>Points</th>
                        <th>RDV</th>
                        <th>Inscrit le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td>
                            <div class="admin-user">
                                <div class="admin-user__avatar {{ $user->isAdmin() ? 'admin-user__avatar--admin' : '' }}">
                                    {{ strtoupper(substr($user->name, 0, 2)) }}
                                </div>
                                {{ $user->name }}
                                @if($user->id === auth()->id())
                                    <span class="admin-badge" style="margin-left:6px">Vous</span>
                                @endif
                            </div>
                        </td>
                        <td class="admin-table__muted">{{ $user->email }}</td>
                        <td>
                            <span class="admin-badge admin-badge--{{ $user->role }}">
                                {{ $user->role === 'admin' ? '🔑 Admin' : '💼 Commercial' }}
                            </span>
                        </td>
                        <td>{{ $user->points }}</td>
                        <td>{{ $user->appointments_count }}</td>
                        <td class="admin-table__muted">{{ $user->created_at->format('d/m/Y') }}</td>
                        <td>
                            <div class="admin-table__actions">
                                <a href="{{ route('admin.users.edit', $user) }}" class="admin-table__action">Modifier</a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                      onsubmit="return confirm('Supprimer {{ $user->name }} ? Ses RDV seront aussi supprimés.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="admin-table__action admin-table__action--danger">Supprimer</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
