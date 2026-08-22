@extends('layouts.backoffice')

@section('title', 'Réservations — Administration')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Réservations</h1>
    <p class="mt-1 text-sm text-gray-500">Suivi de toutes les réservations de la plateforme.</p>
</div>

@php
    $resaLabels = ['en_attente' => 'En attente', 'confirmee' => 'Confirmée', 'annulee' => 'Annulée', 'terminee' => 'Terminée'];
@endphp

@include('backoffice.partials.filters', [
    'action' => route('admin.reservations'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Référence, voyageur, établissement…'],
        ['name' => 'statut', 'label' => 'Statut', 'type' => 'select', 'options' => ['' => 'Tous les statuts'] + $resaLabels],
        ['name' => 'etablissement', 'label' => 'Établissement', 'type' => 'select',
         'options' => ['' => 'Tous les établissements'] + $establishments->pluck('name', 'id')->all()],
    ],
])

<div class="card overflow-x-auto">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Établissement</th>
                <th class="px-5 py-3">Voyageur</th>
                <th class="px-5 py-3">Dates</th>
                <th class="px-5 py-3">Montant</th>
                <th class="px-5 py-3">Statut</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($reservations as $reservation)
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3 font-semibold text-hb-blue">{{ $reservation->establishment->name }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $reservation->user->name }}</td>
                    <td class="px-5 py-3 text-gray-600">
                        {{ optional($reservation->check_in)->format('d/m/Y') }} → {{ optional($reservation->check_out)->format('d/m/Y') }}
                    </td>
                    <td class="px-5 py-3 font-semibold text-hb-blue">{{ number_format($reservation->amount, 0, ',', ' ') }} FCFA</td>
                    <td class="px-5 py-3">
                        <span class="badge-pill {{ match($reservation->status) { 'confirmee' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annulee' => 'bg-red-100 text-red-700', 'terminee' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' } }}">
                            {{ $resaLabels[$reservation->status] ?? $reservation->status }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <form action="{{ route('admin.reservations.update', $reservation->id) }}" method="POST" class="flex justify-end gap-2">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="input-field !w-36 !py-1.5 text-xs">
                                @foreach($resaLabels as $statusValue => $statusLabel)
                                    <option value="{{ $statusValue }}" @selected($reservation->status === $statusValue)>{{ $statusLabel }}</option>
                                @endforeach
                            </select>
<button type="submit" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-arrow-repeat"></i> MAJ</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucune réservation.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6">
    {{ $reservations->links() }}
</div>
@endsection
