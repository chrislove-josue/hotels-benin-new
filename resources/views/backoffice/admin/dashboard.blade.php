@extends('layouts.backoffice')

@section('title', 'Tableau de bord — Administration')

@section('content')
<div class="mb-8">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Tableau de bord</h1>
    <p class="mt-1 text-sm text-gray-500">Vue d'ensemble de la plateforme Hôtels Bénin.</p>
</div>

{{-- Cartes statistiques --}}
<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-blue/10 text-xl text-hb-blue"><i class="bi bi-building"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue">{{ $stats['establishments'] }}</p>
            <p class="text-xs text-gray-500">Établissements</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-orange/10 text-xl text-hb-orange"><i class="bi bi-people"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue">{{ $stats['users'] }}</p>
            <p class="text-xs text-gray-500">Utilisateurs</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-gold/20 text-xl text-hb-gold"><i class="bi bi-calendar-check"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue">{{ $stats['reservations'] }}</p>
            <p class="text-xs text-gray-500">Réservations</p>
            <p class="text-[11px] text-hb-orange">{{ $stats['pending_reservations'] }} en attente</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-xl text-green-600"><i class="bi bi-credit-card"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue">{{ number_format($stats['revenue'], 0, ',', ' ') }} FCFA</p>
            <p class="text-xs text-gray-500">Revenus (paiements réussis)</p>
            <p class="text-[11px] text-green-600">Commission : {{ number_format($stats['commission'], 0, ',', ' ') }} FCFA</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-gold/10 text-xl text-hb-gold"><i class="bi bi-clipboard-check"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue">{{ $stats['pending_changes'] }}</p>
            <p class="text-xs text-gray-500">Modifications en attente</p>
            <a href="{{ route('admin.pending') }}" class="text-[11px] font-semibold text-hb-orange hover:underline">Examiner</a>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    {{-- Dernières réservations --}}
    <div class="card lg:col-span-2">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h2 class="font-display text-lg font-bold text-hb-blue">Dernières réservations</h2>
            <a href="{{ route('admin.reservations') }}" class="text-xs font-semibold text-hb-orange hover:underline">Tout voir</a>
        </div>
        <div class="divide-y divide-gray-50">
            @forelse($latestReservations as $reservation)
                <div class="flex items-center justify-between gap-3 px-5 py-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-800">{{ $reservation->establishment->name }}</p>
                        <p class="text-xs text-gray-500">{{ $reservation->user->name }} · {{ $reservation->created_at->format('d/m/Y') }}</p>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        <span class="text-sm font-bold text-hb-blue">{{ number_format($reservation->amount, 0, ',', ' ') }} FCFA</span>
                        <span class="badge-pill {{ match($reservation->status) { 'confirmee' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annulee' => 'bg-red-100 text-red-700', 'terminee' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' } }}">
                            {{ __($reservation->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <p class="px-5 py-6 text-center text-sm text-gray-400">Aucune réservation pour le moment.</p>
            @endforelse
        </div>
    </div>

    <div class="space-y-6">
        {{-- Établissements en attente --}}
        <div class="card">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="font-display text-lg font-bold text-hb-blue">Valorisation en attente</h2>
                <p class="text-xs text-gray-500">{{ $stats['unread_messages'] }} message(s) non lu(s)</p>
            </div>
            <div class="flex items-center justify-between gap-3 border-b border-gray-50 px-5 py-3">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Comptes en attente</p>
                    <p class="text-xs text-gray-400">Hôtes & voyageurs à valider</p>
                </div>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-1 text-xs font-bold text-hb-orange">
                    {{ $stats['pending_users'] }} <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($pendingEstablishments as $item)
                    <div class="flex items-center justify-between gap-2 px-5 py-3">
                        <p class="truncate text-sm text-gray-700">{{ $item->name }}</p>
                        <a href="{{ route('admin.establishments') }}" class="text-xs font-semibold text-hb-orange">Traiter</a>
                    </div>
                @empty
                    <p class="px-5 py-6 text-center text-sm text-gray-400">Rien en attente.</p>
                @endforelse
            </div>
        </div>

        {{-- Revenus par mois --}}
        <div class="card p-5">
            <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Revenus par mois</h2>
            <div class="space-y-2">
                @forelse($revenueByMonth as $row)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">{{ \Carbon\Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('M Y') }}</span>
                        <span class="font-semibold text-hb-blue">{{ number_format($row->total, 0, ',', ' ') }} FCFA</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-400">Aucune donnée.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
