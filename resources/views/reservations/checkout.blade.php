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
                    <dd class="font-semibold text-gray-700">{{ optional($reservation->check_in)->format('d/m/Y') }} → {{ optional($reservation->check_out)->format('d/m/Y') }} ({{ $reservation->nights() }} nuits)</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Voyageurs</dt>
                    <dd class="font-semibold text-gray-700">{{ $reservation->guests }}</dd>
                </div>
                <div class="mt-3 flex justify-between border-t border-gray-100 pt-3">
                    <dt class="font-semibold text-hb-blue">Total à payer</dt>
                    <dd class="font-display text-xl font-bold text-hb-blue">{{ number_format($reservation->amount, 0, ',', ' ') }} FCFA</dd>
                </div>
            </dl>
        </div>

        {{-- Formulaire de paiement simulé --}}
        <div class="card p-6">
            <div class="mb-4 flex items-center gap-2">
                <span class="flex h-8 w-8 items-center justify-center rounded-full bg-hb-gold/20 text-hb-orange"><i class="bi bi-credit-card"></i></span>
                <h2 class="font-display text-lg font-bold text-hb-blue">Choisissez votre moyen de paiement</h2>
            </div>

            <form action="{{ route('reservations.process', $reservation) }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach($methods as $key => $label)
                        <label class="flex cursor-pointer items-center gap-3 rounded-xl border-2 border-gray-200 bg-white p-4 transition hover:border-hb-gold hover:bg-hb-cream has-[:checked]:border-hb-gold has-[:checked]:bg-hb-gold/10">
                            <input type="radio" name="method" value="{{ $key }}"
                                   class="h-4 w-4 accent-hb-orange" required @checked($loop->first)>
                            <span class="text-sm font-semibold text-gray-700">{{ $label }}</span>
                        </label>
                    @endforeach
                </div>

                <div class="rounded-xl bg-hb-sand p-4 text-xs text-gray-500">
                    <p class="flex items-center gap-2"><i class="bi bi-info-circle text-hb-orange"></i> Paiement simulé — aucun débit réel ne sera effectué dans cette démonstration.</p>
                </div>

                <button type="submit" class="btn-primary w-full">
                    <i class="bi bi-check-circle"></i> Payer {{ number_format($reservation->amount, 0, ',', ' ') }} FCFA
                </button>
            </form>
        </div>

        <a href="{{ route('reservations.show', $reservation) }}" class="text-center text-sm font-semibold text-hb-blue hover:underline">
            <i class="bi bi-arrow-left"></i> Retour à la réservation
        </a>
    </div>
</section>
@endsection
