@extends('layouts.backoffice')

@section('title', 'Utilisateurs — Administration')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Utilisateurs</h1>
        <p class="mt-1 text-sm text-gray-500">Créez, modifiez, supprimez et gérez les comptes de la plateforme.</p>
        @if($pendingUsers = $users->where('is_approved', false)->count())
            <p class="mt-1 text-xs font-semibold text-hb-orange">{{ $pendingUsers }} compte(s) en attente d'approbation</p>
        @endif
    </div>
    <a href="{{ route('admin.users.create') }}" class="btn-primary !px-5 !py-2.5 text-sm">
        <i class="bi bi-person-plus"></i> Nouvel utilisateur
    </a>
</div>

<form method="GET" action="{{ route('admin.users') }}" class="card mb-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:flex-wrap">
        <div class="sm:min-w-[240px] sm:flex-1">
            <label class="label">Rechercher</label>
            <div class="relative">
                <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom, e-mail, téléphone…"
                    class="input-field !pl-9">
            </div>
        </div>
        <div>
            <label class="label">Rôle</label>
            <select name="role" class="input-field">
                <option value="">Tous les rôles</option>
                <option value="voyageur" @selected(request('role') === 'voyageur')>Voyageur</option>
                <option value="hote" @selected(request('role') === 'hote')>Hôte</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            </select>
        </div>
        <div>
            <label class="label">Statut</label>
            <select name="statut" class="input-field">
                <option value="">Tous les statuts</option>
                <option value="en_attente" @selected(request('statut') === 'en_attente')>En attente</option>
                <option value="approuve" @selected(request('statut') === 'approuve')>Approuvé</option>
                <option value="desactive" @selected(request('statut') === 'desactive')>Désactivé</option>
            </select>
        </div>
    </div>
    <div class="flex items-center gap-2 sm:shrink-0">
        <button type="submit" class="btn-primary !px-4 !py-2.5 text-sm">
            <i class="bi bi-funnel"></i> Filtrer
        </button>
        @if(request()->hasAny(['search', 'role', 'statut']))
            <a href="{{ route('admin.users') }}" class="btn-outline !px-4 !py-2.5 text-sm">
                <i class="bi bi-x-circle"></i> Réinitialiser
            </a>
        @endif
    </div>
</form>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[900px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Utilisateur</th>
                <th class="px-5 py-3">Téléphone / WhatsApp</th>
                <th class="px-5 py-3">Rôle</th>
                <th class="px-5 py-3">Statut</th>
                <th class="px-5 py-3">Établissements</th>
                <th class="px-5 py-3">Réservations</th>
                <th class="px-5 py-3">Inscrit le</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($users as $user)
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-hb-blue text-sm font-bold text-white">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-hb-blue">{{ $user->name }}</p>
                                <p class="truncate text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @if($user->phone)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="inline-flex items-center gap-1 font-mono text-xs font-semibold text-green-600 hover:underline">
                                <i class="bi bi-whatsapp"></i> {{ $user->phone }}
                            </a>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">
                        <span class="badge-pill {{ $user->role === 'admin' ? 'bg-hb-gold/20 text-hb-blue' : ($user->role === 'hote' ? 'bg-hb-orange/10 text-hb-orange-dark' : 'bg-gray-100 text-gray-600') }}">
                            {{ $user->role }}
                        </span>
                    </td>
<td class="px-5 py-3">
                        @if($user->isAdmin())
                            <span class="badge-pill bg-hb-blue/10 text-hb-blue">Admin</span>
                        @elseif($user->is_approved)
                            <span class="badge-pill bg-green-100 text-green-700"><i class="bi bi-check-circle"></i> Approuvé</span>
                        @else
                            <span class="badge-pill bg-yellow-100 text-yellow-700"><i class="bi bi-hourglass-split"></i> En attente</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $user->establishments_count }}</td>
                    <td class="px-5 py-3 text-center text-gray-600">{{ $user->reservations_count }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $user->created_at->format('d/m/Y') }}</td>
<td class="px-5 py-3">
                        <div class="flex flex-wrap items-center justify-end gap-1.5">
                            @unless($user->isAdmin())
                                @if(! $user->is_approved)
                                    <form action="{{ route('admin.users.approve', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-green-100 text-green-700 transition hover:bg-green-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-1" title="Approuver">
                                            <i class="bi bi-check-circle text-base"></i>
                                        </button>
                                    </form>
                                @else
                                    <form action="{{ route('admin.users.reject', $user->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-orange-100 text-orange-700 transition hover:bg-orange-500 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-1" title="Désactiver">
                                            <i class="bi bi-pause-circle text-base"></i>
                                        </button>
                                    </form>
                                @endif
                            @endunless

                            <a href="{{ route('admin.users.edit', $user->id) }}" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-hb-blue/10 text-hb-blue transition hover:bg-hb-blue hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-hb-blue focus-visible:ring-offset-1" title="Modifier">
                                <i class="bi bi-pencil text-base"></i>
                            </a>

                            @if($user->id !== auth()->id())
                                <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                                      onsubmit="return confirm('Supprimer définitivement cet utilisateur ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-red-100 text-red-600 transition hover:bg-red-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1" title="Supprimer">
                                        <i class="bi bi-trash text-base"></i>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-gray-400">Aucun utilisateur.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $users->links() }}
</div>
@endsection
