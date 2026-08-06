@extends('layouts.backoffice')

@section('title', 'Tableau de bord — Espace hôte')

@section('content')
<div class="mb-8 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Bonjour, {{ auth()->user()->name }} 👋</h1>
        <p class="mt-1 text-sm text-gray-500">Gérez vos établissements et vos réservations.</p>
    </div>
<a href="{{ route('hote.establishments.create') }}" class="btn-gold"><i class="bi bi-plus-lg"></i> Nouvel établissement</a>
</div>

{{-- Statistiques --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-blue/10 text-xl text-hb-blue"><i class="bi bi-building"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue">{{ $establishments->count() }}</p>
            <p class="text-xs text-gray-500">Vos établissements</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-orange/10 text-xl text-hb-orange"><i class="bi bi-calendar-check"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue">{{ $totalReservations }}</p>
            <p class="text-xs text-gray-500">Réservations reçues</p>
            <p class="text-[11px] text-hb-orange">{{ $pendingReservations }} en attente</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-xl text-green-600"><i class="bi bi-credit-card"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue">{{ number_format($monthlyRevenue, 0, ',', ' ') }} FCFA</p>
            <p class="text-xs text-gray-500">Revenus (réservations confirmées)</p>
        </div>
    </div>
</div>

{{-- Mes établissements --}}
<div class="mt-6 space-y-4">
    <h2 class="font-display text-xl font-bold text-hb-blue">Mes établissements</h2>

    @forelse($establishments as $establishment)
        <div class="card p-4">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center">
                <img src="{{ $establishment->cover_image }}" alt="" class="h-20 w-full rounded-xl object-cover sm:w-32">
                <div class="min-w-0 flex-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-display font-bold text-hb-blue">{{ $establishment->name }}</p>
                        <span class="badge-pill {{ $establishment->is_available ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                            {{ $establishment->is_available ? 'En ligne' : 'En attente de validation' }}
                        </span>
                    </div>
                    <p class="mt-1 text-sm text-gray-500">{{ $establishment->type->name }} · {{ $establishment->locality->name }}</p>
                    <p class="mt-1 text-xs text-gray-400">{{ $establishment->rooms_count ?? $establishment->rooms->count() }} chambre(s) · {{ $establishment->reservations->count() }} réservation(s)</p>
                </div>
                <div class="flex shrink-0 gap-2">
                    <a href="{{ route('establishments.show', $establishment->slug) }}" class="btn-outline !px-3 !py-1.5 text-xs" target="_blank">Voir</a>
                    <a href="{{ route('hote.establishments.edit', $establishment->id) }}" class="btn-primary !px-3 !py-1.5 text-xs">Gérer</a>
                </div>
            </div>
        </div>
    @empty
        <div class="card p-10 text-center">
            <p class="text-lg font-semibold text-hb-blue">Aucun établissement pour le moment.</p>
            <p class="mt-2 text-sm text-gray-500">Créez votre premier établissement pour commencer.</p>
<a href="{{ route('hote.establishments.create') }}" class="btn-gold mt-5"><i class="bi bi-plus-lg"></i> Créer mon établissement</a>
        </div>
    @endforelse
</div>
@endsection
