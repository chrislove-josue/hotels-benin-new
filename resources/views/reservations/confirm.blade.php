@extends('layouts.app')

@section('title', 'Réservation enregistrée — Hôtels Bénin')

@section('content')
<section class="mx-auto max-w-2xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="card overflow-hidden">
        <div class="bg-gradient-to-r from-hb-blue to-hb-blue-deeper p-8 text-center text-white">
            <div class="mx-auto mb-4 flex h-20 w-20 items-center justify-center rounded-full bg-hb-gold text-hb-blue-deeper">
                <i class="bi bi-check-lg text-4xl"></i>
            </div>
            <h1 class="font-display text-2xl font-bold">Réservation enregistrée !</h1>
            <p class="mt-2 text-sm text-white/80">Votre réservation est en attente de paiement.</p>
            <p class="mt-4 font-mono text-sm font-semibold text-hb-gold">{{ $reservation->reference }}</p>
        </div>

        <div class="p-8">
            <div class="mb-6 rounded-xl bg-hb-cream p-5 text-sm">
                <div class="flex items-center gap-3">
                    <img src="{{ $reservation->establishment->cover_image }}" alt="{{ $reservation->establishment->name }}"
                         class="h-14 w-14 rounded-xl object-cover">
                    <div>
                        <p class="font-semibold text-hb-blue">{{ $reservation->establishment->name }}</p>
                        <p class="text-xs text-gray-500">{{ $reservation->establishment->locality->name }}</p>
                    </div>
                </div>
                <dl class="mt-4 space-y-2">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Chambre</dt>
                        <dd class="font-semibold text-gray-700">{{ $reservation->room->name ?? '—' }}</dd>
                    </div>
<div class="flex justify-between">
                        <dt class="text-gray-500">Séjour</dt>
                        <dd class="font-semibold text-gray-700">{{ optional($reservation->check_in)->format('d/m/Y') }} → {{ optional($reservation->check_out)->format('d/m/Y') }} ({{ $reservation->nights() }} {{ $reservation->nights() > 1 ? 'jours' : 'jour' }})</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Prix unitaire</dt>
                        <dd class="font-semibold text-gray-700">{{ number_format($reservation->room->price_per_night, 0, ',', ' ') }} FCFA / jour</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Voyageurs</dt>
                        <dd class="font-semibold text-gray-700">{{ $reservation->guests }}</dd>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2">
                        <dt class="font-semibold text-hb-blue">Total <span class="text-xs font-normal text-gray-500">({{ number_format($reservation->room->price_per_night, 0, ',', ' ') }} × {{ $reservation->nights() }})</span></dt>
                        <dd class="font-display text-lg font-bold text-hb-blue">{{ number_format($reservation->amount, 0, ',', ' ') }} FCFA</dd>
                    </div>
                </dl>
            </div>

            <div class="flex flex-col gap-3 sm:flex-row">
                <a href="{{ route('reservations.checkout', $reservation) }}" class="btn-primary flex-1">
                    <i class="bi bi-credit-card"></i> Procéder au paiement
                </a>
                <a href="{{ route('reservations.index') }}" class="btn-outline flex-1">
                    <i class="bi bi-list-check"></i> Mes réservations
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
