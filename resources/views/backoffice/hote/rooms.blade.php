@extends('layouts.backoffice')

@section('title', 'Chambres — ' . $establishment->name)

@section('content')
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="{{ route('hote.dashboard') }}" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange hover:underline"><i class="bi bi-arrow-left"></i> Retour au tableau de bord</a>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Chambres de {{ $establishment->name }}</h1>
        <p class="mt-1 text-sm text-gray-500">Gérez les chambres et espaces. Chaque ajout ou modification doit être approuvé par un administrateur.</p>
    </div>
</div>

{{-- Formulaire d'ajout --}}
<div class="card mb-6 p-6">
    <h2 class="mb-4 flex items-center gap-2 font-display text-lg font-bold text-hb-blue"><i class="bi bi-plus-circle"></i> Ajouter une chambre / un espace</h2>
    <form action="{{ route('hote.rooms.store', $establishment->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <div>
            <label class="label">Nom *</label>
            <input type="text" name="name" class="input-field" placeholder="Ex : Chambre double" required>
        </div>
        <div>
            <label class="label">Description</label>
            <input type="text" name="description" class="input-field">
        </div>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
            <div>
                <label class="label">Capacité *</label>
                <input type="number" name="capacity" class="input-field" min="1" value="2" required>
            </div>
            <div>
                <label class="label">Prix / nuit *</label>
                <input type="number" name="price_per_night" class="input-field" min="0" required>
            </div>
<div>
                <label class="label">Quantité *</label>
                <input type="number" name="quantity" class="input-field" min="1" value="1" required>
            </div>
        </div>
        <div>
            <label class="label">Image de couverture de la chambre (fichier)</label>
            <input type="file" name="cover_image_file" accept="image/*" class="input-field !py-2">
            <p class="mt-1 text-xs text-gray-400">JPG, PNG ou WebP — 5 Mo max.</p>
        </div>
        <div>
            <label class="label">Ou URL de l'image de couverture</label>
            <input type="url" name="cover_image" class="input-field" placeholder="https://...">
        </div>
        <button type="submit" class="btn-gold"><i class="bi bi-check-lg"></i> Ajouter la chambre</button>
    </form>
</div>

{{-- Liste des chambres --}}
<div class="card overflow-x-auto">
    <div class="border-b border-gray-100 bg-hb-cream px-5 py-4">
        <h2 class="font-display text-lg font-bold text-hb-blue">Chambres existantes ({{ $establishment->rooms->count() }})</h2>
    </div>
    <table class="w-full min-w-[680px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Nom</th>
                <th class="px-5 py-3">Description</th>
                <th class="px-5 py-3">Capacité</th>
                <th class="px-5 py-3">Prix / nuit</th>
                <th class="px-5 py-3">Quantité</th>
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
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('hote.rooms.edit', [$establishment->id, $room->id]) }}" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-pencil-square"></i> Modifier</a>
                            <form action="{{ route('hote.rooms.destroy', [$establishment->id, $room->id]) }}" method="POST" onsubmit="return confirm('Demander la suppression de cette chambre ? Elle sera supprimée après approbation admin.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucune chambre pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
