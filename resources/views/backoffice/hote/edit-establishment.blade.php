@extends('layouts.backoffice')

@section('title', 'Gérer l\'établissement — Espace hôte')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">{{ $establishment->name }}</h1>
    <p class="mt-1 text-sm text-gray-500">Modifiez les informations et gérez les chambres / espaces.</p>
</div>

@if ($errors->any())
    <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">
        <ul class="list-inside list-disc space-y-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    {{-- Formulaire d'édition --}}
    <form action="{{ route('hote.establishments.update', $establishment->id) }}" method="POST" class="card p-6 space-y-4">
        @csrf
        @method('PUT')

        <h2 class="font-display text-lg font-bold text-hb-blue">Informations</h2>

        <div>
            <label class="label">Nom *</label>
            <input type="text" name="name" value="{{ old('name', $establishment->name) }}" class="input-field" required>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Type *</label>
                <select name="establishment_type_id" class="input-field" required>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" @selected($establishment->establishment_type_id == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Localité *</label>
                <select name="locality_id" class="input-field" required>
                    @foreach($localities as $locality)
                        <option value="{{ $locality->id }}" @selected($establishment->locality_id == $locality->id)>{{ $locality->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div>
            <label class="label">Adresse *</label>
            <input type="text" name="address" value="{{ old('address', $establishment->address) }}" class="input-field" required>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Prix / nuit *</label>
                <input type="number" name="price_from" value="{{ old('price_from', $establishment->price_from) }}" class="input-field" min="0" required>
            </div>
            <div>
                <label class="label">Capacité *</label>
                <input type="number" name="capacity" value="{{ old('capacity', $establishment->capacity) }}" class="input-field" min="1" required>
            </div>
        </div>
        <div>
            <label class="label">Image de couverture (URL)</label>
            <input type="url" name="cover_image" value="{{ old('cover_image', $establishment->cover_image) }}" class="input-field">
        </div>
        <div>
            <label class="label">Courte description *</label>
            <input type="text" name="short_description" value="{{ old('short_description', $establishment->short_description) }}" class="input-field" required>
        </div>
        <div>
            <label class="label">Description *</label>
            <textarea name="description" rows="5" class="input-field" required>{{ old('description', $establishment->description) }}</textarea>
        </div>

        <div>
            <label class="label">Équipements</label>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                @foreach($amenities as $amenity)
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:border-hb-blue">
                        <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange"
                            @checked($establishment->amenities->contains($amenity->id))>
                        {{ $amenity->name }}
                    </label>
                @endforeach
            </div>
        </div>

<button type="submit" class="btn-primary w-full"><i class="bi bi-check-lg"></i> Enregistrer les modifications</button>
    </form>

    {{-- Gestion des chambres --}}
    <div class="space-y-6">
        <div class="card p-6">
            <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Ajouter une chambre / un espace</h2>
            <form action="{{ route('hote.rooms.store', $establishment->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="label">Nom *</label>
                    <input type="text" name="name" class="input-field" placeholder="Ex : Chambre double" required>
                </div>
                <div>
                    <label class="label">Description</label>
                    <input type="text" name="description" class="input-field">
                </div>
                <div class="grid grid-cols-3 gap-3">
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
<button type="submit" class="btn-gold w-full"><i class="bi bi-plus-lg"></i> Ajouter la chambre</button>
            </form>
        </div>

        <div class="card">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="font-display text-lg font-bold text-hb-blue">Chambres existantes ({{ $establishment->rooms->count() }})</h2>
            </div>
            <div class="divide-y divide-gray-50">
                @forelse($establishment->rooms as $room)
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-hb-blue">{{ $room->name }}</p>
                                <p class="text-xs text-gray-500">{{ $room->description }}</p>
                                <p class="mt-1 text-xs text-gray-400">Capacité {{ $room->capacity }} pers. · <span class="font-semibold text-hb-blue">{{ number_format($room->price_per_night, 0, ',', ' ') }} FCFA</span> / nuit</p>
                            </div>
                            <form action="{{ route('hote.rooms.destroy', [$establishment->id, $room->id]) }}" method="POST" onsubmit="return confirm('Supprimer cette chambre ?');">
                                @csrf
                                @method('DELETE')
<button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <p class="px-5 py-6 text-center text-sm text-gray-400">Aucune chambre pour le moment.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
