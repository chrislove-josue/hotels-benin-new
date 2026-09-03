@extends('layouts.app')

@section('title', 'Paiement — '.$reservation->reference.' — Hôtels Bénin')

@section('content')
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <nav class="mb-6 flex items-center gap-1 text-sm text-gray-500">
        <a href="{{ route('home') }}" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="{{ route('reservations.show', $reservation) }}" class="hover:text-hb-blue">{{ $reservation->reference }}</a> <span>/</span>
        <span class="font-semibold text-hb-blue">Paiement</span>
    </nav>

    <div class="mb-6 text-center">
        <h1 class="font-display text-3xl font-bold text-hb-blue">Paiement {{ $sandbox ? 'de test' : '' }}</h1>
        <p class="mt-1 text-sm text-gray-500">
            Réservation <span class="font-mono font-semibold text-hb-orange">{{ $reservation->reference }}</span> ·
            {{ $reservation->establishment->name }}
        </p>
        <p class="mt-3 font-display text-2xl font-bold text-hb-blue">{{ number_format($reservation->amount, 0, ',', ' ') }} FCFA</p>
    </div>

    <div class="card p-6 text-center">
        <p class="mb-6 text-sm text-gray-600">
            <i class="bi bi-credit-card"></i>
            La fenêtre de paiement KKiaPay va s'ouvrir. Effectuez le règlement par mobile money ou carte bancaire.
        </p>

        {{-- Bouton de secours si le widget ne s'ouvre pas automatiquement --}}
        <button type="button" id="kkiapay-pay"
            class="btn-primary w-full">
            <i class="bi bi-wallet2"></i> Ouvrir le paiement — {{ number_format($reservation->amount, 0, ',', ' ') }} FCFA
        </button>

        <div id="kkiapay-spinner" class="mt-4 hidden items-center justify-center gap-2 text-sm text-gray-500">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-hb-blue border-t-transparent"></span>
            En attente du paiement…
        </div>

        <a href="{{ route('reservations.show', $reservation) }}" class="mt-6 inline-block text-center text-sm font-semibold text-hb-blue hover:underline">
            <i class="bi bi-arrow-left"></i> Annuler et revenir à la réservation
        </a>
    </div>
</section>

<script src="https://cdn.kkiapay.me/k.js"></script>
@php
    $jsAmount = number_format((float) $reservation->amount, 0, '.', '');
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const callbackUrl = @json($callbackUrl);
        const widgetAmount = {{ $jsAmount }};
        const stateData = { reservation_id: {{ $reservation->id }} };

        @if (empty($publicKey))
            // Configuration KKiaPay manquante : on informe le voyageur au lieu d'échouer en silence.
            const missing = document.createElement('div');
            missing.className = 'mt-4 rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-700';
            missing.innerHTML = '<i class="bi bi-exclamation-triangle"></i> ' +
                'Le paiement en ligne est temporairement indisponible. Réessayez dans quelques instants ' +
                'ou contactez l\'établissement.';
            document.querySelector('.card')?.prepend(missing);
            document.getElementById('kkiapay-pay')?.remove();
            return;
        @endif

        const openWidget = function () {
            openKkiapayWidget({
                amount: widgetAmount,
                key: @json($publicKey),
                sandbox: {{ $sandbox ? 'true' : 'false' }},
                data: JSON.stringify(stateData),
                name: @json($reservation->user?->name ?? 'Client'),
                email: @json($reservation->user?->email ?? ''),
                callback: callbackUrl,
                theme: '#1A3A6B',
                position: 'center',
                countries: ['BJ'],
                paymentmethod: 'momo,card'
            });

            document.getElementById('kkiapay-spinner')?.classList.remove('hidden');
            document.getElementById('kkiapay-spinner')?.classList.add('flex');
        };

        // Redirection vers le callback avec l'identifiant de transaction.
        addSuccessListener(function (response) {
            const transactionId = response && (response.transactionId || response.id);
            let url = callbackUrl;
            if (transactionId) {
                url += (url.includes('?') ? '&' : '?') + 'transactionId=' + encodeURIComponent(transactionId);
            }
            window.location.href = url;
        });

        document.getElementById('kkiapay-pay').addEventListener('click', openWidget);

        // Ouvre automatiquement le widget dès le chargement de la page.
        setTimeout(openWidget, 400);
    });
</script>
@endsection
