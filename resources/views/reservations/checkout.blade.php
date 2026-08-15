@extends('layouts.app')

@section('title', 'Paiement — '.$reservation->reference.' — Hôtels Bénin')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <nav class="mb-6 flex items-center gap-1 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="{{ route('reservations.show', $reservation) }}" class="hover:text-hb-blue">{{ $reservation->reference }}</a> <span>/</span>
        <span class="font-semibold text-hb-blue">Paiement</span>
    </nav>

    <div class="mb-6">
        <h1 class="font-display text-3xl font-bold text-hb-blue">Paiement de votre réservation</h1>
        <p class="mt-1 text-sm text-gray-500">Réservation <span class="font-mono font-semibold text-hb-orange">{{ $reservation->reference }}</span> · {{ $reservation->establishment->name }}</p>
    </div>

    <div class="grid grid-cols-1 gap-6">
        {{-- Récapitulatif --}}
        <div class="card p-6">
            <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Récapitulatif</h2>
            <dl class="space-y-3 text-sm">
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
                <div class="mt-3 flex justify-between border-t border-gray-100 pt-3">
                    <dt class="font-semibold text-hb-blue">Total à payer <span class="text-xs font-normal text-gray-500">({{ number_format($reservation->room->price_per_night, 0, ',', ' ') }} × {{ $reservation->nights() }})</span></dt>
                    <dd class="font-display text-xl font-bold text-hb-blue">{{ number_format($reservation->amount, 0, ',', ' ') }} FCFA</dd>
                </div>
            </dl>
        </div>

        @if ($errors->any())
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                @foreach ($errors->all() as $error)
                    <p class="flex items-center gap-2"><i class="bi bi-exclamation-triangle"></i> {{ $error }}</p>
                @endforeach
            </div>
        @endif

        {{-- Formulaire de paiement --}}
        <div class="card p-6">
           

            <form action="{{ route('reservations.process', $reservation) }}" method="POST" class="space-y-4">
                @csrf


                <button type="submit" class="btn-primary w-full">
                    <i class="bi bi-check-circle"></i> Continuer vers le paiement — {{ number_format($reservation->amount, 0, ',', ' ') }} FCFA
                </button>
            </form>
        </div>

        <a href="{{ route('reservations.show', $reservation) }}" class="text-center text-sm font-semibold text-hb-blue hover:underline">
            <i class="bi bi-arrow-left"></i> Retour à la réservation
        </a>
    </div>
</section>
@endsection