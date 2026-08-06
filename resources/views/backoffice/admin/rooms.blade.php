@extends('layouts.backoffice')

@section('title', 'Chambres — ' . $establishment->name)

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="{{ route('admin.establishments') }}" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange hover:underline"><i class="bi bi-arrow-left"></i> Retour aux établissements</a>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Chambres de {{ $establishment->name }}</h1>
        <p class="mt-1 text-sm text-gray-500">{{ $establishment->rooms->count() }} chambre(s) / espace(s).</p>
    </div>
</div>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[680px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Nom</th>
                <th class="px-5 py-3">Description</th>
                <th class="px-5 py-3">Capacité</th>
<th class="px-5 py-3">Prix / nuit</th>
                <th class="px-5 py-3">Quantité</th>
                <th class="px-5 py-3">Disponible</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            @forelse($establishment->rooms as $room)
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3 font-semibold text-hb-blue">{{ $room->name }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $room->description }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $room->capacity }} pers.</td>
                    <td class="px-5 py-3 font-semibold text-hb-blue">{{ number_format($room->price_per_night, 0, ',', ' ') }} FCFA</td>
                    <td class="px-5 py-3 text-gray-600">{{ $room->quantity }}</td>
<td class="px-5 py-3">
                        <span class="badge-pill {{ $room->is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                            {{ $room->is_available ? 'Oui' : 'Non' }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('admin.rooms.edit', [$establishment->id, $room->id]) }}" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-pencil-square"></i> Modifier</a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucune chambre pour cet établissement.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
