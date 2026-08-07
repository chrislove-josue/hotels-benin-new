@extends('layouts.app')

@section('title', 'Événements & programmes au Bénin — Hôtels Bénin')
@section('meta_description', 'Découvrez les festivals, conférences et événements culturels au Bénin. Planifiez votre séjour autour des grands rendez-vous du pays.')

@section('content')
<section class="relative overflow-hidden border-b border-white/60 bg-gradient-to-br from-hb-blue to-hb-blue-deeper py-14">
    <div class="pointer-events-none absolute -right-20 top-0 h-64 w-64 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-64 w-64 rounded-full bg-hb-orange/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-reveal>
        <span class="badge-gold-solid mb-4"><i class="bi bi-calendar-event"></i> Agenda du pays</span>
        <h1 class="font-display text-4xl font-bold text-white">Événements & programmes au Bénin</h1>
        <div class="mt-3 h-1 w-16 rounded-full bg-gradient-to-r from-hb-gold to-hb-orange"></div>
        <p class="mt-3 max-w-2xl text-white/80">
            Festivals, conférences, compétitions sportives et célébrations culturelles — trouvez l'événement qui vous donnera envie de réserver votre séjour.
        </p>
    </div>
</section>

{{-- Prochains événements à la une --}}
@if($upcoming->isNotEmpty())
<section class="mx-auto max-w-7xl px-4 pt-12 sm:px-6 lg:px-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <span class="section-eyebrow">À ne pas manquer</span>
            <h2 class="font-display text-2xl font-bold text-hb-blue-deeper">Prochains rendez-vous</h2>
            <div class="divider-gold"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4" data-reveal>
        @foreach($upcoming as $event)
            <a href="{{ route('events.show', $event->slug) }}" class="card group overflow-hidden">
                <div class="relative h-44 overflow-hidden">
                    <img src="{{ $event->cover_image }}" alt="{{ $event->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy">
                    <div class="absolute inset-0 bg-gradient-to-t from-hb-blue-deeper/80 to-transparent"></div>
                    <span class="absolute left-3 top-3 flex items-center gap-1 rounded-full bg-hb-gold px-3 py-1 text-[11px] font-bold text-hb-blue-deeper shadow">
                        <i class="bi {{ $event->category_icon }}"></i> {{ $event->category_label }}
                    </span>
                    <div class="absolute bottom-3 left-3 right-3">
                        <p class="flex items-center gap-1 text-[11px] font-semibold text-hb-gold">
                            <i class="bi bi-calendar3"></i>
                            {{ $event->date_start->format('d M Y') }}{{ $event->date_end && $event->date_end->ne($event->date_start) ? ' → '.$event->date_end->format('d M Y') : '' }}
                        </p>
                    </div>
                </div>
                <div class="p-5">
                    <h3 class="font-display text-lg font-bold leading-snug text-hb-blue transition group-hover:text-hb-orange">{{ $event->title }}</h3>
                    <p class="mt-1 flex items-center gap-1 text-xs text-gray-500"><i class="bi bi-geo-alt text-hb-orange"></i> {{ $event->location }}</p>
                    <p class="mt-2 line-clamp-2 text-sm text-gray-600">{{ $event->short_description }}</p>
                </div>
            </a>
        @endforeach
    </div>
</section>
@endif

{{-- Liste avec filtres --}}
<section class="mx-auto max-w-7xl px-4 py-12 sm:px-6 lg:px-8"
         x-data="{ filtersOpen: false }">
    <div class="mb-6 flex flex-col items-start gap-3 md:flex-row md:items-end md:justify-between">
        <div>
            <span class="section-eyebrow">Agenda complet</span>
            <h2 class="font-display text-2xl font-bold text-hb-blue-deeper">Tous les événements</h2>
            <div class="divider-gold"></div>
        </div>

        <button @click="filtersOpen = !filtersOpen"
                class="btn-outline-blue !px-4 !py-2 text-xs md:hidden">
            <i class="bi bi-funnel"></i> Filtrer par catégorie
        </button>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">
        {{-- Filtres --}}
        <aside class="lg:col-span-1 lg:sticky lg:top-24 lg:h-fit">
            <div class="card space-y-2 p-5" :class="filtersOpen ? 'block' : 'hidden'" x-show="filtersOpen" x-cloak>
                <h3 class="mb-3 font-display text-lg font-bold text-hb-blue-deeper">Catégories</h3>
                <form action="{{ route('events.index') }}" method="GET" class="space-y-2">
                    <div>
                        <a href="{{ route('events.index') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm transition {{ empty($filters['category']) ? 'bg-hb-blue text-white font-semibold' : 'text-gray-600 hover:bg-hb-cream' }}">
                            <i class="bi bi-grid"></i> Tous les événements
                        </a>
                    </div>
                    @foreach($categories as $key => $label)
                        <div>
                            <a href="{{ route('events.index', ['category' => $key]) }}"
                               class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm transition {{ ($filters['category'] ?? '') === $key ? 'bg-hb-blue text-white font-semibold' : 'text-gray-600 hover:bg-hb-cream' }}">
                                <i class="bi {{ match($key) { 'festival' => 'bi-music-note-beamed', 'culture' => 'bi-palette', 'sport' => 'bi-trophy', 'affaires' => 'bi-briefcase', 'religieux' => 'bi-buildings', default => 'bi-calendar-event' } }}"></i> {{ $label }}
                            </a>
                        </div>
                    @endforeach

                    <div class="border-t border-gray-100 pt-4">
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Recherche</label>
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="input-field !py-2 text-sm" placeholder="Ville, nom...">
                            <button type="submit" class="btn-primary shrink-0 !px-4 !py-2 text-xs"><i class="bi bi-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </aside>

        {{-- Résultats --}}
        <div class="lg:col-span-3">
            @if($events->isEmpty())
                <div class="card p-10 text-center">
                    <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-hb-cream text-hb-blue">
                        <i class="bi bi-calendar-x text-3xl"></i>
                    </div>
                    <p class="text-lg font-semibold text-hb-blue-deeper">Aucun événement trouvé.</p>
                    <p class="mt-2 text-sm text-gray-500">Essayez une autre catégorie ou élargissez votre recherche.</p>
                    <a href="{{ route('events.index') }}" class="btn-outline-blue mt-5 !px-5 !py-2.5 text-sm">Voir tous les événements</a>
                </div>
            @else
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3" data-reveal>
                    @foreach($events as $event)
                        <a href="{{ route('events.show', $event->slug) }}" class="card group overflow-hidden">
                            <div class="relative h-48 overflow-hidden">
                                <img src="{{ $event->cover_image }}" alt="{{ $event->title }}" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy">
                                <div class="absolute inset-0 bg-gradient-to-t from-hb-blue-deeper/70 to-transparent"></div>
                                <span class="absolute left-3 top-3 flex items-center gap-1 rounded-full bg-white/90 px-3 py-1 text-[11px] font-bold text-hb-blue shadow backdrop-blur">
                                    <i class="bi {{ $event->category_icon }} text-hb-orange"></i> {{ $event->category_label }}
                                </span>
                                <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between">
                                    <p class="flex items-center gap-1 text-[11px] font-semibold text-white">
                                        <i class="bi bi-calendar3 text-hb-gold"></i>
                                        {{ $event->date_start->format('d M Y') }}
                                    </p>
                                    @if($event->is_featured)
                                        <span class="rounded-full bg-hb-gold px-2 py-0.5 text-[10px] font-bold text-hb-blue-deeper">★ À la une</span>
                                    @endif
                                </div>
                            </div>
                            <div class="p-5">
                                <h3 class="font-display text-lg font-bold leading-snug text-hb-blue transition group-hover:text-hb-orange">{{ $event->title }}</h3>
                                <p class="mt-1 flex items-center gap-1 text-xs text-gray-500"><i class="bi bi-geo-alt text-hb-orange"></i> {{ $event->location }}</p>
                                <p class="mt-2 line-clamp-2 text-sm text-gray-600">{{ $event->short_description }}</p>
                                <span class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange">En savoir plus <i class="bi bi-arrow-right"></i></span>
                            </div>
                        </a>
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</section>

{{-- CTA réservation --}}
<section class="relative overflow-hidden bg-hb-blue-deeper py-14">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8" data-reveal>
        <span class="badge-gold-solid mb-4"><i class="bi bi-building"></i> Préparez votre séjour</span>
        <h2 class="font-display text-3xl font-bold text-white md:text-4xl">Un événement vous tente ? Réservez votre hébergement dès maintenant.</h2>
        <p class="mx-auto mt-4 mb-8 max-w-2xl text-white/75">
            Hôtels, résidences et villas à proximité des grands rendez-vous du pays. Les meilleures adresses partent vite.
        </p>
        <a href="{{ route('establishments.index') }}" class="btn-gold"><i class="bi bi-search"></i> Trouver un hébergement</a>
    </div>
</section>
@endsection

