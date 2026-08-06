@extends('layouts.app')

@section('title', 'Réservation '.$reservation->reference.' — Hôtels Bénin')

@section('content')
<section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
    <nav class="mb-6 flex items-center gap-1 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="{{ route('reservations.index') }}" class="hover:text-hb-blue">Mes réservations</a> <span>/</span>
        <span class="font-semibold text-hb-blue">{{ $reservation->reference }}</span>
    </nav>

    <div class="card overflow-hidden">
        {{-- En-tête --}}
        <div class="flex flex-col gap-4 border-b border-gray-100 bg-hb-cream p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-mono text-xs font-semibold text-hb-orange">{{ $reservation->reference }}</p>
                <h1 class="mt-1 font-display text-2xl font-bold text-hb-blue">{{ $reservation->establishment->name }}</h1>
                <p class="text-sm text-gray-500">{{ $reservation->establishment->locality->name }}</p>
            </div>
            <span class="badge-pill !px-4 !py-1.5 text-sm {{ match($reservation->status) { 'confirmee' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annulee' => 'bg-red-100 text-red-700', 'terminee' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' } }}">
                {{ $reservation->status }}
            </span>
        </div>

        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
            {{-- Détails du séjour --}}
            <div>
                <h2 class="mb-3 font-display text-lg font-bold text-hb-blue">Détails du séjour</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Établissement</dt>
                        <dd class="font-semibold text-hb-blue">{{ $reservation->establishment->name }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Chambre</dt>
                        <dd class="font-semibold text-gray-700">{{ $reservation->room->name ?? '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Arrivée</dt>
                        <dd class="font-semibold text-gray-700">{{ optional($reservation->check_in)->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Départ</dt>
                        <dd class="font-semibold text-gray-700">{{ optional($reservation->check_out)->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nuits</dt>
                        <dd class="font-semibold text-gray-700">{{ $reservation->nights() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Voyageurs</dt>
                        <dd class="font-semibold text-gray-700">{{ $reservation->guests }}</dd>
                    </div>
                </dl>
            </div>

            {{-- Paiement --}}
            <div>
                <h2 class="mb-3 font-display text-lg font-bold text-hb-blue">Paiement</h2>
                @if($reservation->transaction)
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Montant</dt>
                            <dd class="font-display font-bold text-hb-blue">{{ number_format($reservation->amount, 0, ',', ' ') }} FCFA</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Commission</dt>
                            <dd class="font-semibold text-gray-700">{{ number_format($reservation->commission, 0, ',', ' ') }} FCFA</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Méthode</dt>
                            <dd class="font-semibold text-gray-700">{{ ucfirst(str_replace('_', ' ', $reservation->transaction->method)) }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Référence</dt>
                            <dd class="font-mono text-xs text-gray-600">{{ $reservation->transaction->reference }}</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Statut</dt>
                            <dd>
                                <span class="badge-pill {{ $reservation->transaction->status === 'reussie' ? 'bg-green-100 text-green-700' : ($reservation->transaction->status === 'en_attente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ $reservation->transaction->status }}
                                </span>
                            </dd>
                        </div>
                    </dl>
                @else
                    <p class="text-sm text-gray-500">Aucune transaction associée.</p>
                @endif
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-wrap gap-3 border-t border-gray-100 bg-hb-cream/50 p-6">
            @if($reservation->status === 'en_attente')
                <a href="{{ route('reservations.checkout', $reservation) }}" class="btn-primary">
                    <i class="bi bi-credit-card"></i> Payer maintenant
                </a>
            @endif
            <a href="{{ route('establishments.show', $reservation->establishment->slug) }}" class="btn-outline">
                <i class="bi bi-building"></i> Voir l'établissement
            </a>
        </div>
    </div>
</section>
@endsection
