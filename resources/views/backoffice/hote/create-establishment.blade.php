@extends('layouts.backoffice')

@section('title', 'Nouvel établissement — Espace hôte')

@section('content')
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Nouvel établissement</h1>
    <p class="mt-1 text-sm text-gray-500">Complétez les informations. L'établissement sera visible après validation par un administrateur.</p>
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

<form action="{{ route('hote.establishments.store') }}" method="POST" class="space-y-6">
    @csrf

    <div class="card p-6">
        <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Informations générales</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label">Nom de l'établissement *</label>
                <input type="text" name="name" value="{{ old('name') }}" class="input-field" required>
            </div>
            <div>
                <label class="label">Type d'établissement *</label>
                <select name="establishment_type_id" class="input-field" required>
                    <option value="">Sélectionner...</option>
                    @foreach($types as $type)
                        <option value="{{ $type->id }}" @selected(old('establishment_type_id') == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="label">Localité *</label>
                <select name="locality_id" class="input-field" required>
                    <option value="">Sélectionner...</option>
                    @foreach($localities as $locality)
                        <option value="{{ $locality->id }}" @selected(old('locality_id') == $locality->id)>{{ $locality->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="label">Adresse *</label>
                <input type="text" name="address" value="{{ old('address') }}" class="input-field" placeholder="Quartier, Ville" required>
            </div>
            <div>
                <label class="label">Prix à partir de (FCFA / nuit) *</label>
                <input type="number" name="price_from" value="{{ old('price_from') }}" class="input-field" min="0" required>
            </div>
            <div>
                <label class="label">Capacité (pers.) *</label>
                <input type="number" name="capacity" value="{{ old('capacity', 2) }}" class="input-field" min="1" required>
            </div>
            <div class="sm:col-span-2">
                <label class="label">URL de l'image de couverture</label>
                <input type="url" name="cover_image" value="{{ old('cover_image') }}" class="input-field" placeholder="https://...">
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Description</h2>
        <div class="space-y-4">
            <div>
                <label class="label">Courte description *</label>
                <input type="text" name="short_description" value="{{ old('short_description') }}" class="input-field" maxlength="255" required>
            </div>
            <div>
                <label class="label">Description complète *</label>
                <textarea name="description" rows="6" class="input-field" required>{{ old('description') }}</textarea>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Équipements</h2>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
            @forelse($amenities as $amenity)
                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 transition hover:border-hb-blue">
                    <input type="checkbox" name="amenities[]" value="{{ $amenity->id }}" class="h-4 w-4 rounded border-gray-300 text-hb-orange focus:ring-hb-orange"
                        @checked(in_array($amenity->id, old('amenities', [])))>
                    {{ $amenity->name }}
                </label>
            @empty
                <p class="col-span-full text-sm text-gray-400">Aucun équipement disponible.</p>
            @endforelse
        </div>
    </div>

    <div class="flex items-center gap-3">
<button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Créer l'établissement</button>
        <a href="{{ route('hote.dashboard') }}" class="btn-outline"><i class="bi bi-x-lg"></i> Annuler</a>
    </div>
</form>
@endsection
