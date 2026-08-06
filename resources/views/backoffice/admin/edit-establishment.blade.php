@extends('layouts.backoffice')

@section('title', 'Modifier l\'établissement — Administration')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.establishments') }}" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange hover:underline"><i class="bi bi-arrow-left"></i> Retour aux établissements</a>
    <h1 class="font-display text-3xl font-bold text-hb-blue">Modifier l'établissement</h1>
    <p class="mt-1 text-sm text-gray-500">Modification directe appliquée immédiatement sur la vitrine.</p>
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

<form action="{{ route('admin.establishments.update', $establishment->id) }}" method="POST" class="card p-6 space-y-4">
    @csrf
    @method('PUT')

    <h2 class="flex items-center gap-2 font-display text-lg font-bold text-hb-blue"><i class="bi bi-building"></i> {{ $establishment->name }}</h2>

    <div>
        <label class="label">Nom *</label>
        <input type="text" name="name" value="{{ old('name', $establishment->name) }}" class="input-field" required>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
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
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="label">Prix / nuit (FCFA) *</label>
            <input type="number" name="price_from" value="{{ old('price_from', $establishment->price_from) }}" class="input-field" min="0" required>
        </div>
        <div>
            <label class="label">Capacité (pers.) *</label>
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

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
            <input type="checkbox" name="is_available" value="1" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange" @checked($establishment->is_available)>
            Disponible sur la vitrine
        </label>
        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
            <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 rounded text-hb-gold focus:ring-hb-gold" @checked($establishment->is_featured)>
            <i class="bi bi-star-fill text-hb-gold"></i> À la une
        </label>
    </div>

    <div>
        <label class="label">Équipements</label>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
            @foreach($amenities as $amenity)
                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:border-hb-blue">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange"
                        @checked($establishment->amenities->contains($amenity->id))>
                    {{ $amenity->name }}
                </label>
            @endforeach
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Enregistrer (appliqué immédiatement)</button>
        <a href="{{ route('admin.establishments') }}" class="btn-outline">Annuler</a>
    </div>
</form>
@endsection
