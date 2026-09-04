@extends('layouts.backoffice')

@section('title', 'Utilisateurs — Administration')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Utilisateurs</h1>
        <p class="mt-1 text-sm text-gray-500">Hôtes classés par revenus et chambres · Voyageurs par réservations.</p>
        @if($pendingCount > 0)
            <p class="mt-1 text-xs font-semibold text-hb-orange">{{ $pendingCount }} compte(s) en attente d'approbation</p>
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
            <label class="label">Statut</label>
            <select name="statut" class="input-field">
                <option value="">Tous les statuts</option>
                <option value="en_attente" @selected(request('statut') === 'en_attente')>En attente</option>
                <option value="approuve" @selected(request('statut') === 'approuve')>Approuvé</option>
            </select>
        </div>
    </div>
    <div class="flex items-center gap-2 sm:shrink-0">
        <button type="submit" class="btn-primary !px-4 !py-2.5 text-sm">
            <i class="bi bi-funnel"></i> Filtrer
        </button>
        @if(request()->hasAny(['search', 'statut']))
            <a href="{{ route('admin.users') }}" class="btn-outline !px-4 !py-2.5 text-sm">
                <i class="bi bi-x-circle"></i> Réinitialiser
            </a>
        @endif
    </div>
</form>

@php
    $initialTab = request('role') === 'voyageur' ? 'voyageurs' : (request('role') === 'admin' ? 'admins' : 'hotes');
    $hostIds = $hosts->pluck('id')->values();
    $voyageurIds = $voyageurs->pluck('id')->values();
    $adminIds = $admins->pluck('id')->reject(fn ($id) => $id === auth()->id())->values();
@endphp

<div x-data="{
    tab: '{{ $initialTab }}',
    selected: [],

    toggleAll(ids, checked) {
        ids = Array.from(new Set(ids.map(Number)));
        if (checked) {
            this.selected = Array.from(new Set([...this.selected, ...ids]));
        } else {
            this.selected = this.selected.filter(id => !ids.includes(id));
        }
    },

    isAllChecked(ids) {
        ids = Array.from(new Set(ids.map(Number)));
        return ids.length > 0 && ids.every(id => this.selected.includes(id));
    },

    submitBulk() {
        let ids = Array.from(new Set(this.selected.map(Number))).filter(id => id > 0);
        if (ids.length === 0) return;
        if (!confirm('Supprimer définitivement ' + ids.length + ' utilisateur(s) sélectionné(s) ?')) return;
        document.getElementById('bulk-ids').value = ids.join(',');
        document.getElementById('bulk-delete-form').submit();
    }
}">

    {{-- Onglets --}}
    <div class="mb-4 flex flex-wrap gap-2">
        <button type="button" x-on:click="tab = 'hotes'"
                :class="tab === 'hotes' ? 'bg-hb-blue text-white' : 'bg-white text-gray-600 hover:bg-hb-cream'"
                class="rounded-full px-4 py-2 text-sm font-semibold shadow-sm transition">
            <i class="bi bi-building"></i> Hôtes <span class="ml-1 rounded-full px-1.5 text-xs" :class="tab === 'hotes' ? 'bg-white/20' : 'bg-hb-cream'" x-text="{{ count($hosts) }}"></span>
        </button>
        <button type="button" x-on:click="tab = 'voyageurs'"
                :class="tab === 'voyageurs' ? 'bg-hb-blue text-white' : 'bg-white text-gray-600 hover:bg-hb-cream'"
                class="rounded-full px-4 py-2 text-sm font-semibold shadow-sm transition">
            <i class="bi bi-suitcase-lg"></i> Voyageurs <span class="ml-1 rounded-full px-1.5 text-xs" :class="tab === 'voyageurs' ? 'bg-white/20' : 'bg-hb-cream'" x-text="{{ count($voyageurs) }}"></span>
        </button>
        <button type="button" x-on:click="tab = 'admins'"
                :class="tab === 'admins' ? 'bg-hb-blue text-white' : 'bg-white text-gray-600 hover:bg-hb-cream'"
                class="rounded-full px-4 py-2 text-sm font-semibold shadow-sm transition">
            <i class="bi bi-shield-lock"></i> Admins <span class="ml-1 rounded-full px-1.5 text-xs" :class="tab === 'admins' ? 'bg-white/20' : 'bg-hb-cream'" x-text="{{ count($admins) }}"></span>
        </button>
    </div>

    {{-- Barre de suppression groupée --}}
    <div x-show="selected.length > 0" x-cloak
         class="mb-4 flex flex-col gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 sm:flex-row sm:items-center sm:justify-between">
        <p class="text-sm font-semibold text-red-700">
            <i class="bi bi-check2-square"></i> <span x-text="selected.length"></span> utilisateur(s) sélectionné(s)
        </p>
        <div class="flex items-center gap-2">
            <button type="button" @click="selected = []" class="btn-outline !px-4 !py-2 text-xs">Annuler la sélection</button>
            <button type="button" @click="submitBulk()" class="btn-danger !px-4 !py-2 text-xs">
                <i class="bi bi-trash"></i> Supprimer la sélection
            </button>
        </div>
    </div>

    {{-- ======== TABLEAU DES HÔTES ======== --}}
    <div x-show="tab === 'hotes'" x-cloak>
        <div class="card overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-sm">
                <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">
                            <input type="checkbox" class="h-4 w-4 accent-hb-blue"
                                   :checked="isAllChecked({{ $hostIds->toJson() }})"
                                   @change="toggleAll({{ $hostIds->toJson() }}, $event.target.checked)">
                        </th>
                        <th class="px-5 py-3">Rang</th>
                        <th class="px-5 py-3">Hôte</th>
                        <th class="px-5 py-3">Téléphone</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3 text-center">Établissements</th>
                        <th class="px-5 py-3 text-center">Chambres</th>
                        <th class="px-5 py-3 text-center">Réservations reçues</th>
                        <th class="px-5 py-3 text-right">Revenus encaissés</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($hosts as $user)
                        <tr class="hover:bg-hb-cream/50">
                            <td class="px-5 py-3">
                                <input type="checkbox" class="h-4 w-4 accent-hb-blue" value="{{ $user->id }}" x-model="selected">
                            </td>
                            <td class="px-5 py-3">
                                @if($loop->index === 0)
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-hb-gold to-hb-orange text-sm text-white"><i class="bi bi-trophy"></i></span>
                                @elseif($loop->index < 3)
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-hb-gold/15 text-sm font-bold text-hb-gold">{{ $loop->iteration }}</span>
                                @else
                                    <span class="pl-2 font-bold text-gray-400">{{ $loop->iteration }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-hb-orange/15 text-sm font-bold text-hb-orange-dark">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-hb-blue">{{ $user->name }}</p>
                                        <p class="truncate text-xs text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @if($user->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="inline-flex items-center gap-1 font-mono text-xs font-semibold text-green-600 hover:underline"><i class="bi bi-whatsapp"></i> {{ $user->phone }}</a>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if($user->is_approved)
                                    <span class="badge-pill bg-green-100 text-green-700"><i class="bi bi-check-circle"></i> Approuvé</span>
                                @else
                                    <span class="badge-pill bg-yellow-100 text-yellow-700"><i class="bi bi-hourglass-split"></i> En attente</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center font-semibold text-gray-700">{{ $user->establishments_count }}</td>
                            <td class="px-5 py-3 text-center font-bold text-hb-blue">{{ $user->rooms_count }}</td>
                            <td class="px-5 py-3 text-center text-gray-600">{{ $user->host_reservations_count }}</td>
                            <td class="px-5 py-3 text-right font-bold text-green-600">{{ number_format((float) $user->revenue, 0, ',', ' ') }} FCFA</td>
                            <td class="px-5 py-3">@include('backoffice.admin.partials.user-actions', ['user' => $user])</td>
                        </tr>
                    @empty
                        <tr><td colspan="10" class="px-5 py-8 text-center text-gray-400">Aucun hôte.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ======== TABLEAU DES VOYAGEURS ======== --}}
    <div x-show="tab === 'voyageurs'" x-cloak>
        <div class="card overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">
                            <input type="checkbox" class="h-4 w-4 accent-hb-blue"
                                   :checked="isAllChecked({{ $voyageurIds->toJson() }})"
                                   @change="toggleAll({{ $voyageurIds->toJson() }}, $event.target.checked)">
                        </th>
                        <th class="px-5 py-3">Rang</th>
                        <th class="px-5 py-3">Voyageur</th>
                        <th class="px-5 py-3">Téléphone</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3 text-center">Réservations</th>
                        <th class="px-5 py-3 text-right">Total dépensé</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($voyageurs as $user)
                        <tr class="hover:bg-hb-cream/50">
                            <td class="px-5 py-3">
                                <input type="checkbox" class="h-4 w-4 accent-hb-blue" value="{{ $user->id }}" x-model="selected">
                            </td>
                            <td class="px-5 py-3">
                                @if($loop->index === 0)
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-hb-gold to-hb-orange text-sm text-white"><i class="bi bi-trophy"></i></span>
                                @elseif($loop->index < 3)
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-hb-gold/15 text-sm font-bold text-hb-gold">{{ $loop->iteration }}</span>
                                @else
                                    <span class="pl-2 font-bold text-gray-400">{{ $loop->iteration }}</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full {{ $user->role === 'admin' ? 'bg-hb-gold/20 text-hb-blue' : ($user->role === 'hote' ? 'bg-hb-orange/15 text-hb-orange-dark' : 'bg-hb-blue/10 text-hb-blue') }} text-sm font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-hb-blue">{{ $user->name }}
                                            @if($user->role === 'hote')
                                                <span class="badge-pill ml-1 bg-hb-orange/10 text-hb-orange-dark" title="Cet utilisateur est aussi un hôte"><i class="bi bi-building"></i> aussi hôte</span>
                                            @elseif($user->role === 'admin')
                                                <span class="badge-pill ml-1 bg-hb-gold/20 text-hb-blue" title="Cet utilisateur est aussi un administrateur"><i class="bi bi-shield-lock"></i> admin</span>
                                            @endif
                                        </p>
                                        <p class="truncate text-xs text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @if($user->phone)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $user->phone) }}" target="_blank" class="inline-flex items-center gap-1 font-mono text-xs font-semibold text-green-600 hover:underline"><i class="bi bi-whatsapp"></i> {{ $user->phone }}</a>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @if($user->is_approved)
                                    <span class="badge-pill bg-green-100 text-green-700"><i class="bi bi-check-circle"></i> Approuvé</span>
                                @else
                                    <span class="badge-pill bg-yellow-100 text-yellow-700"><i class="bi bi-hourglass-split"></i> En attente</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-center font-bold text-hb-blue">{{ $user->voyageur_reservations_count }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-green-600">{{ number_format((float) $user->spent, 0, ',', ' ') }} FCFA</td>
                            <td class="px-5 py-3">@include('backoffice.admin.partials.user-actions', ['user' => $user])</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="px-5 py-8 text-center text-gray-400">Aucun voyageur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ======== TABLEAU DES ADMINS ======== --}}
    <div x-show="tab === 'admins'" x-cloak>
        <div class="card overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">
                            <input type="checkbox" class="h-4 w-4 accent-hb-blue"
                                   :checked="isAllChecked({{ $adminIds->toJson() }})"
                                   @change="toggleAll({{ $adminIds->toJson() }}, $event.target.checked)">
                        </th>
                        <th class="px-5 py-3">Administrateur</th>
                        <th class="px-5 py-3">Téléphone</th>
                        <th class="px-5 py-3">Inscrit le</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($admins as $user)
                        <tr class="hover:bg-hb-cream/50">
                            <td class="px-5 py-3">
                                @if($user->id !== auth()->id())
                                    <input type="checkbox" class="h-4 w-4 accent-hb-blue" value="{{ $user->id }}" x-model="selected">
                                @else
                                    <span class="inline-block h-4 w-4"></span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-hb-gold/20 text-sm font-bold text-hb-blue">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-hb-blue">{{ $user->name }} @if($user->id === auth()->id())<span class="badge-pill ml-1 bg-hb-blue/10 text-hb-blue">vous</span>@endif</p>
                                        <p class="truncate text-xs text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                @if($user->phone)
                                    {{ $user->phone }}
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-5 py-3 text-gray-600">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-right text-xs text-gray-400">
                                @unless($user->id === auth()->id())
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-pencil-square"></i> Modifier</a>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400">Aucun administrateur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Formulaire caché de soumission pour la suppression groupée --}}
<form id="bulk-delete-form" method="POST" action="{{ route('admin.users.destroy.bulk') }}" class="hidden">
    @csrf
    <input type="hidden" id="bulk-ids" name="ids">
</form>
@endsection
