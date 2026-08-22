@extends('layouts.backoffice')

@section('title', 'Transactions — Administration')

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Transactions</h1>
        <p class="mt-1 text-sm text-gray-500">Historique des paiements et commissions — mise à jour manuelle possible (espèces, remboursement).</p>
    </div>
    @if($total)
        <div class="card px-5 py-3 text-right">
            <p class="text-xs text-gray-500">Total encaissé</p>
            <p class="text-lg font-bold text-hb-blue">{{ number_format($total, 0, ',', ' ') }} FCFA</p>
        </div>
    @endif
</div>

@if (session('status'))
    <div class="mb-4 rounded-xl bg-emerald-50 p-4 text-sm font-medium text-emerald-700">
        {{ session('status') }}
    </div>
@endif

@php
    $txLabels = ['reussie' => 'Réussie', 'en_attente' => 'En attente', 'echouee' => 'Échouée', 'remboursee' => 'Remboursée'];
    $txStyles = [
        'reussie' => 'bg-green-100 text-green-700',
        'en_attente' => 'bg-yellow-100 text-yellow-700',
        'echouee' => 'bg-red-100 text-red-700',
        'remboursee' => 'bg-purple-100 text-purple-700',
    ];
    $methodLabels = [
        '' => 'Toutes les méthodes',
        'fedapay' => 'FedaPay (mobile money / carte)',
        'moov_money' => 'Moov Money',
        'mtn_money' => 'MTN MoMo',
        'sur_place' => 'Sur place (espèces)',
    ];
@endphp

@include('backoffice.partials.filters', [
    'action' => route('admin.transactions'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Référence transaction ou réservation…'],
        ['name' => 'statut', 'label' => 'Statut', 'type' => 'select', 'options' => ['' => 'Tous les statuts'] + $txLabels],
        ['name' => 'methode', 'label' => 'Méthode', 'type' => 'select', 'options' => $methodLabels],
    ],
])

<div class="card overflow-x-auto">
    <table class="w-full min-w-[980px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Référence</th>
                <th class="px-5 py-3">Réservation / Voyageur</th>
                <th class="px-5 py-3">Méthode</th>
                <th class="px-5 py-3">Montant</th>
                <th class="px-5 py-3">Commission</th>
                <th class="px-5 py-3">Statut &amp; raison</th>
                <th class="px-5 py-3">Date</th>
                <th class="px-5 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($transactions as $transaction)
                <tr class="align-top hover:bg-hb-cream/50">
                    <td class="px-5 py-3 font-mono text-xs text-gray-600">{{ $transaction->reference }}</td>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-hb-blue">{{ $transaction->reservation?->reference ?? '#' . $transaction->reservation_id }}</p>
                        <p class="text-xs text-gray-500">{{ $transaction->reservation?->establishment?->name }}</p>
                        <p class="text-xs text-gray-400">{{ $transaction->reservation?->user?->name }}</p>
                    </td>
                    <td class="px-5 py-3 capitalize text-gray-600">{{ $transaction->method ?? '—' }}</td>
                    <td class="px-5 py-3 font-semibold text-hb-blue">{{ number_format($transaction->amount, 0, ',', ' ') }} FCFA</td>
                    <td class="px-5 py-3 text-gray-600">{{ number_format($transaction->commission, 0, ',', ' ') }} FCFA</td>
                    <td class="px-5 py-3">
                        <span class="badge-pill {{ $txStyles[$transaction->status] ?? 'bg-gray-100 text-gray-600' }}">
                            {{ $txLabels[$transaction->status] ?? $transaction->status }}
                        </span>
                        @if ($transaction->reason)
                            <p class="mt-1 max-w-[200px] text-[11px] italic text-gray-500" title="{{ $transaction->reason }}">« {{ $transaction->reason }} »</p>
                        @endif
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-xs text-gray-600">{{ $transaction->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-5 py-3">
                        <form action="{{ route('admin.transactions.update', $transaction->id) }}" method="POST" class="flex w-64 flex-col gap-2" x-data="{ status: '{{ $transaction->status }}' }">
                            @csrf
                            @method('PATCH')
                            <select name="status" x-model="status" class="input-field !py-1.5 text-xs">
                                @foreach ($txLabels as $value => $label)
                                    <option value="{{ $value }}" @selected($transaction->status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <input type="text" name="reason" value="{{ $transaction->reason }}" placeholder="Raison (ex : payé en espèces)" maxlength="255"
                                   class="input-field !py-1.5 text-xs"
                                   :required="['reussie','echouee','remboursee'].includes(status)">
                            <button type="submit" class="btn-primary !py-1.5 text-xs"><i class="bi bi-save"></i> Mettre à jour</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-gray-400">Aucune transaction.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $transactions->links() }}
</div>
@endsection
