@extends('layouts.backoffice')

@section('title', isset($event) ? 'Modifier l\'événement — Administration' : 'Nouvel événement — Administration')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.events') }}" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange hover:underline">
        <i class="bi bi-arrow-left"></i> Retour aux événements
    </a>
    <h1 class="font-display text-3xl font-bold text-hb-blue">{{ isset($event) ? "Modifier : {$event->title}" : 'Nouvel événement' }}</h1>
    <p class="mt-1 text-sm text-gray-500">{{ isset($event) ? 'Modification appliquée immédiatement sur la vitrine publique.' : 'Créez un événement, festival ou programme du Bénin.' }}</p>
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

<form action="{{ isset($event) ? route('admin.events.update', $event->id) : route('admin.events.store') }}" method="POST" class="card p-6 space-y-4">
    @csrf
    @if(isset($event))
        @method('PUT')
    @endif

    <div>
        <label class="label">Titre *</label>
        <input type="text" name="title" value="{{ old('title', $event->title ?? '') }}" class="input-field" required>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="label">Catégorie *</label>
            <select name="category" class="input-field" required>
                @foreach($categories as $value => $label)
                    <option value="{{ $value }}" @selected(old('category', $event->category ?? '') === $value)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="label">Lieu</label>
            <input type="text" name="location" value="{{ old('location', $event->location ?? '') }}" class="input-field">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="label">Date de début *</label>
            <input type="date" name="date_start" value="{{ old('date_start', isset($event) && $event->date_start ? $event->date_start->format('Y-m-d') : '') }}" class="input-field" required>
        </div>
        <div>
            <label class="label">Date de fin</label>
            <input type="date" name="date_end" value="{{ old('date_end', isset($event) && $event->date_end ? $event->date_end->format('Y-m-d') : '') }}" class="input-field">
        </div>
    </div>

    <div>
        <label class="label">Image de couverture (URL)</label>
        <input type="url" name="cover_image" value="{{ old('cover_image', $event->cover_image ?? '') }}" class="input-field" placeholder="https://...">
    </div>

    <div>
        <label class="label">Courte description</label>
        <input type="text" name="short_description" value="{{ old('short_description', $event->short_description ?? '') }}" class="input-field">
    </div>

    <div>
        <label class="label">Description *</label>
        <textarea name="description" rows="6" class="input-field" required>{{ old('description', $event->description ?? '') }}</textarea>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
            <input type="checkbox" name="is_available" value="1" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange" @checked(old('is_available', $event->is_available ?? true))>
            Visible sur la vitrine publique
        </label>
        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
            <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 rounded text-hb-gold focus:ring-hb-gold" @checked(old('is_featured', $event->is_featured ?? false))>
            <i class="bi bi-star-fill text-hb-gold"></i> À la une
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> {{ isset($event) ? 'Enregistrer' : 'Créer l\'événement' }}</button>
        <a href="{{ route('admin.events') }}" class="btn-outline">Annuler</a>
    </div>
</form>
@endsection
