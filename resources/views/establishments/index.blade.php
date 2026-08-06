@extends('layouts.app')

@section('title', 'Tous les établissements — Hôtels Bénin')

@section('content')
<section class="relative overflow-hidden border-b border-white/60 bg-gradient-to-br from-hb-blue to-hb-blue-deeper py-12">
    <div class="pointer-events-none absolute -right-20 top-0 h-64 w-64 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-reveal>
        <span class="badge-gold-solid mb-4">Catalogue</span>
        <h1 class="font-display text-4xl font-bold text-white">Tous nos établissements</h1>
        <div class="mt-3 h-1 w-16 rounded-full bg-gradient-to-r from-hb-gold to-hb-orange"></div>
        <p class="mt-3 text-white/80">{{ $establishments->total() }} établissement(s) disponible(s) au Bénin</p>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
         x-data="{ filtersOpen: true }">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

{{-- Filtres --}}
        <aside class="lg:col-span-1 lg:sticky lg:top-24 lg:h-fit lg:self-start">
            {{-- Bouton accordéon (mobile) --}}
<button @click="filtersOpen = !filtersOpen"
                    class="mb-4 flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold text-hb-blue lg:hidden">
                Filtrer les résultats
                <i class="bi bi-chevron-down transition" :class="filtersOpen ? 'rotate-180' : ''"></i>
            </button>

            <form action="{{ route('establishments.index') }}" method="GET"
                  class="card space-y-5 p-5" :class="filtersOpen ? 'block' : 'hidden'" x-show="filtersOpen" x-cloak>
                <h2 class="font-display text-lg font-bold text-hb-blue-deeper">Affiner votre recherche</h2>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Localité</label>
                    <select name="locality" class="input-field">
                        <option value="">Toutes les villes</option>
                        @foreach($localities as $locality)
                            <option value="{{ $locality->slug }}" @selected(($filters['locality'] ?? '') === $locality->slug)>{{ $locality->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Type d'établissement</label>
                    <select name="type" class="input-field">
                        <option value="">Tous les types</option>
                        @foreach($types as $type)
                            <option value="{{ $type->slug }}" @selected(($filters['type'] ?? '') === $type->slug)>{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Prix min</label>
                        <input type="number" name="min_price" value="{{ $filters['min_price'] ?? '' }}" class="input-field" placeholder="0">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Prix max</label>
                        <input type="number" name="max_price" value="{{ $filters['max_price'] ?? '' }}" class="input-field" placeholder="200000">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Capacité minimale</label>
                    <input type="number" name="capacity" value="{{ $filters['capacity'] ?? '' }}" class="input-field" placeholder="Ex : 2">
                </div>

                <button type="submit" class="btn-primary w-full">Appliquer les filtres</button>
                <a href="{{ route('establishments.index') }}" class="block text-center text-sm font-semibold text-gray-500 hover:text-hb-blue">Réinitialiser</a>
            </form>
        </aside>

        {{-- Résultats --}}
        <div class="lg:col-span-3">
{{-- Barre de tri --}}
            <div class="mb-6 flex items-center justify-between gap-3">
                <p class="text-sm text-gray-600">
                    <span class="font-bold text-hb-blue">{{ $establishments->total() }}</span> résultat(s)
                </p>
                <form method="GET" action="{{ route('establishments.index') }}" id="sort-form" class="flex items-center gap-2">
                    @foreach($filters as $key => $value)
                        @if($key !== 'sort' && $value !== '' && $value !== null)
                            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                        @endif
                    @endforeach
                    <label for="sort" class="text-xs font-semibold text-gray-500">Trier :</label>
                    <select id="sort" name="sort" @change="document.getElementById('sort-form').submit()" class="input-field !w-auto !py-2 text-sm">
                        <option value="latest" @selected(($filters['sort'] ?? '') === 'latest')>Plus récents</option>
                        <option value="rating" @selected(($filters['sort'] ?? '') === 'rating')>Mieux notés</option>
                        <option value="price_asc" @selected(($filters['sort'] ?? '') === 'price_asc')>Prix croissant</option>
                        <option value="price_desc" @selected(($filters['sort'] ?? '') === 'price_desc')>Prix décroissant</option>
                    </select>
                </form>
            </div>

            @if($establishments->isEmpty())
                <div class="card p-10 text-center">
                    <p class="text-lg font-semibold text-hb-blue-deeper">Aucun établissement ne correspond à ces critères.</p>
                    <p class="mt-2 text-sm text-gray-500">Essayez d'élargir votre recherche.</p>
                    <a href="{{ route('establishments.index') }}" class="btn-outline-blue mt-5 !px-5 !py-2.5 text-sm">Réinitialiser les filtres</a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3" data-reveal>
                    @foreach($establishments as $establishment)
                        @include('partials.establishment-card', ['establishment' => $establishment])
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $establishments->links() }}
                </div>
            @endif
        </div>
    </div>
</section>
@endsection
