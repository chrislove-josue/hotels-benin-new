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
<form action="{{ route('hote.establishments.update', $establishment->id) }}" method="POST" enctype="multipart/form-data" class="card p-6 space-y-4">
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
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Commission plateforme (%)</label>
                <select name="commission_pct" class="input-field">
                    @foreach([15, 20, 25, 30] as $value)
                        <option value="{{ $value }}" @selected((float) old('commission_pct', $establishment->commission_pct) == $value)>{{ $value }}%</option>
                    @endforeach
                </select>
                <p class="mt-1 text-xs text-gray-400">Part prélevée sur chaque réservation.</p>
            </div>
            <div></div>
        </div>
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Latitude</label>
                <input type="text" name="latitude" value="{{ old('latitude', $establishment->latitude) }}" class="input-field" placeholder="ex: 6.4969">
            </div>
            <div>
                <label class="label">Longitude</label>
                <input type="text" name="longitude" value="{{ old('longitude', $establishment->longitude) }}" class="input-field" placeholder="ex: 2.6289">
                <p class="mt-1 text-xs text-gray-400">Google Maps → clic droit → « Les coordonnées ».</p>
            </div>
        </div>
<div>
            <label class="label">Image de couverture (fichier)</label>
            @if($establishment->cover_image)
                <img src="{{ $establishment->cover_image }}" alt="Couverture" class="mb-2 h-32 w-full rounded-xl object-cover">
            @endif
            <input type="file" name="cover_image_file" accept="image/*" class="input-field !py-2">
            <p class="mt-1 text-xs text-gray-400">JPG, PNG ou WebP — 5 Mo max. Laissez vide pour conserver l'image actuelle.</p>
        </div>
        <div>
            <label class="label">Ou URL de l'image de couverture</label>
            <input type="url" name="cover_image" value="{{ old('cover_image', $establishment->cover_image) }}" class="input-field">
        </div>
        <div>
            <label class="label">Ajouter des photos à la galerie</label>
            <input type="file" name="images[]" accept="image/*" multiple class="input-field !py-2">
            @if($establishment->images->isNotEmpty())
                <div class="mt-2 flex flex-wrap gap-2">
                    @foreach($establishment->images as $image)
                        <img src="{{ $image->path }}" alt="" class="h-16 w-20 rounded-lg object-cover">
                    @endforeach
                </div>
                <p class="mt-1 text-xs text-gray-400">Les nouvelles photos seront ajoutées à cette galerie.</p>
            @else
                <p class="mt-1 text-xs text-gray-400">Aucune photo de galerie pour le moment.</p>
            @endif
        </div>
        <div>
            <label class="label">Courte description *</label>
            <input type="text" name="short_description" value="{{ old('short_description', $establishment->short_description) }}" class="input-field" required>
        </div>
        <div>
            <label class="label">Description *</label>
            <textarea name="description" rows="5" class="input-field" required>{{ old('description', $establishment->description) }}</textarea>
        </div>

        <div x-data="{ transferOn: {{ $establishment->transfer_available ? 'true' : 'false' }} }">
            <h3 class="mb-3 font-display text-sm font-bold text-hb-blue"><i class="bi bi-car-front"></i> Service automobile (transfert aéroport / navette)</h3>
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 transition hover:border-hb-blue">
                <input type="checkbox" name="transfer_available" value="1" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange" x-model="transferOn" @checked($establishment->transfer_available)>
                <span class="text-sm font-semibold text-gray-700">Je propose un service d'accueil / transfert pour les voyageurs</span>
            </label>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2" x-show="transferOn" x-transition>
                <div>
                    <label class="label">Prix du transfert (FCFA)</label>
                    <input type="number" name="transfer_fee" value="{{ old('transfer_fee', $establishment->transfer_fee) }}" class="input-field" min="0" placeholder="ex: 10000">
                </div>
                <div>
                    <label class="label">Description du trajet</label>
                    <input type="text" name="transfer_description" value="{{ old('transfer_description', $establishment->transfer_description) }}" class="input-field" maxlength="255" placeholder="ex: Aéroport Cadjèhoun → établissement">
                </div>
            </div>
            <p class="mt-1 text-xs text-gray-400" x-show="transferOn">Le voyageur verra cette information sur la fiche et pourra vous contacter directement via WhatsApp.</p>
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

{{-- Gestion des chambres (page dédiée) --}}
    <div class="space-y-6">
        <div class="card p-6">
            <h2 class="mb-2 flex items-center gap-2 font-display text-lg font-bold text-hb-blue"><i class="bi bi-door-open"></i> Chambres & espaces</h2>
            <p class="text-sm text-gray-500">Gérez les chambres de cet établissement : ajout, modification et suppression. Chaque changement doit être approuvé par un administrateur.</p>
            <div class="mt-4 flex items-center gap-3">
                <span class="badge-pill bg-hb-blue/10 text-hb-blue">{{ $establishment->rooms->count() }} chambre(s)</span>
                <a href="{{ route('hote.rooms.index', $establishment->id) }}" class="btn-gold !px-4 !py-2 text-xs"><i class="bi bi-gear"></i> Gérer les chambres</a>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="mb-2 flex items-center gap-2 font-display text-lg font-bold text-hb-blue"><i class="bi bi-info-circle"></i> Modération</h2>
            <p class="text-sm text-gray-500">Les modifications des informations de cet établissement sont mises en file d'attente et seront appliquées publiquement uniquement après approbation par un administrateur.</p>
        </div>
    </div>
</div>
@endsection
