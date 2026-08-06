@extends('layouts.app')

@section('title', 'Hôtels Bénin — Trouvez votre séjour idéal')

@section('content')

{{-- HERO --}}
<section class="relative overflow-hidden bg-hb-blue-deeper">
    <div class="absolute inset-0 opacity-30">
        <img src="https://images.unsplash.com/photo-1566073771259-6a8506099945?w=1600&q=80" alt="" class="h-full w-full object-cover">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-hb-blue-deeper via-hb-blue/85 to-hb-blue/40"></div>
    <div class="pointer-events-none absolute -right-32 top-0 h-96 w-96 rounded-full bg-hb-gold/20 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 py-20 sm:px-6 md:py-28 lg:px-8">
        <div class="max-w-2xl">
<span class="badge-gold-solid mb-6"><i class="bi bi-stars"></i> La vitrine des hébergements du Bénin</span>
            <h1 class="font-display text-4xl font-bold leading-tight text-white md:text-5xl lg:text-6xl">
                Réservez votre séjour au <span class="bg-gradient-to-r from-hb-gold to-hb-orange bg-clip-text text-transparent">Bénin</span>,
                en toute simplicité
            </h1>
            <p class="mt-5 max-w-xl text-lg text-white/85">
                Hôtels, résidences meublées, villas et salles de fête — comparez les meilleures adresses de Cotonou à Natitingou.
            </p>

            {{-- Moteur de recherche premium --}}
            <form action="{{ route('establishments.index') }}" method="GET" class="mt-10 grid gap-3 rounded-3xl bg-white/95 p-4 shadow-card-lg backdrop-blur sm:grid-cols-4">
                <div class="sm:col-span-1">
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Localité</label>
                    <select name="locality" class="input-field">
                        <option value="">Toutes les villes</option>
                        @foreach($localities as $locality)
                            <option value="{{ $locality->slug }}">{{ $locality->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Type d'établissement</label>
                    <select name="type" class="input-field">
                        <option value="">Tous les types</option>
                        @foreach($types as $type)
                            <option value="{{ $type->slug }}">{{ $type->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Budget max (FCFA/nuit)</label>
                    <input type="number" name="max_price" placeholder="Ex : 50000" class="input-field">
                </div>
                <div class="flex items-end sm:col-span-1">
<button type="submit" class="btn-primary w-full">
                        <i class="bi bi-search"></i>
                        Rechercher
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Bandeau stats --}}
    <div class="relative border-t border-white/10 bg-white/5 backdrop-blur">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-8 sm:px-6 md:grid-cols-4 lg:px-8">
            @foreach([
                ['value' => $localities->count(), 'suffix' => '+', 'label' => 'Villes couvertes'],
                ['value' => $featured->count() + $topRated->count(), 'suffix' => '+', 'label' => 'Établissements'],
                ['value' => '4.8', 'suffix' => '/5', 'label' => 'Note moyenne'],
                ['value' => '24h', 'suffix' => '', 'label' => 'Réponse support'],
            ] as $stat)
                <div class="text-center" data-reveal>
                    <p class="font-display text-3xl font-bold text-hb-gold">{{ $stat['value'] }}<span class="text-hb-orange">{{ $stat['suffix'] }}</span></p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-white/60">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- LOCALITÉS POPULAIRES --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex flex-col items-start gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <span class="section-eyebrow">Destinations</span>
            <h2 class="section-title">Explorez le Bénin par ville</h2>
            <div class="divider-gold"></div>
        </div>
<a href="{{ route('establishments.index') }}" class="btn-outline-blue !px-5 !py-2.5 text-sm">Voir toutes les villes <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4" data-reveal>
        @foreach($localities as $locality)
            <a href="{{ route('establishments.index', ['locality' => $locality->slug]) }}"
               class="card group flex flex-col items-center justify-center gap-2 p-6 text-center">
<i class="bi bi-geo-alt text-3xl text-hb-orange transition duration-300 group-hover:scale-125 group-hover:-translate-y-1"></i>
                <span class="font-display font-bold text-hb-blue group-hover:text-hb-orange">{{ $locality->name }}</span>
                <span class="text-xs text-gray-400">{{ $locality->establishments_count }} établissement(s)</span>
                <span class="mt-1 h-0.5 w-0 bg-gradient-to-r from-hb-gold to-hb-orange transition-all duration-300 group-hover:w-10"></span>
            </a>
        @endforeach
    </div>
</section>

{{-- ÉTABLISSEMENTS À LA UNE --}}
@if($featured->isNotEmpty())
<section class="relative overflow-hidden bg-white py-16">
    <div class="pointer-events-none absolute -left-24 top-10 h-72 w-72 rounded-full bg-hb-gold/10 blur-3xl"></div>
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-10 flex flex-col items-start gap-4 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="section-eyebrow">Coups de cœur</span>
                <h2 class="section-title">Établissements à la une</h2>
                <div class="divider-gold"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3" data-reveal>
            @foreach($featured as $establishment)
                @include('partials.establishment-card', ['establishment' => $establishment])
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- LES MIEUX NOTÉS --}}
<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex flex-col items-start gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <span class="section-eyebrow">Sélection</span>
            <h2 class="section-title">Les mieux notés en ce moment</h2>
            <div class="divider-gold"></div>
        </div>
<a href="{{ route('establishments.index') }}" class="btn-outline-blue !px-5 !py-2.5 text-sm">Voir tous les établissements <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
        @foreach($topRated as $establishment)
            @include('partials.establishment-card', ['establishment' => $establishment, 'compact' => true])
        @endforeach
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden bg-hb-blue-deeper py-16">
    <div class="pointer-events-none absolute -right-24 top-0 h-80 w-80 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-80 w-80 rounded-full bg-hb-orange/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8" data-reveal>
        <span class="badge-gold-solid mb-4">Devenir hôte</span>
        <h2 class="font-display text-3xl font-bold text-white md:text-4xl">Vous gérez un hôtel ou une salle de fête ?</h2>
        <p class="mx-auto mt-4 mb-8 max-w-2xl text-white/75">
            Rejoignez Hôtels Bénin et faites découvrir votre établissement à des milliers de voyageurs.
        </p>
        <a href="{{ route('register.show') }}" class="btn-gold">Devenir hôte partenaire</a>
    </div>
</section>

@endsection
