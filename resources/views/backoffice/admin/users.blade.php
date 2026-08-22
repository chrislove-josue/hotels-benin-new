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
@endphp

<div x-data="{ tab: '{{ $initialTab }}' }">
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

    {{-- ======== TABLEAU DES HÔTES ======== --}}
    <div x-show="tab === 'hotes'" x-cloak>
        <div class="card overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-sm">
                <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
                    <tr>
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
                        <tr><td colspan="9" class="px-5 py-8 text-center text-gray-400">Aucun hôte.</td></tr>
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
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-hb-blue/10 text-sm font-bold text-hb-blue">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
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
                            <td class="px-5 py-3 text-center font-bold text-hb-blue">{{ $user->voyageur_reservations_count }}</td>
                            <td class="px-5 py-3 text-right font-semibold text-green-600">{{ number_format((float) $user->spent, 0, ',', ' ') }} FCFA</td>
                            <td class="px-5 py-3">@include('backoffice.admin.partials.user-actions', ['user' => $user])</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucun voyageur.</td></tr>
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
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">Aucun administrateur.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
