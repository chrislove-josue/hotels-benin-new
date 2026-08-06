<header x-data="{ mobileOpen: false, dropdownOpen: false, searchOpen: false }"
        class="sticky top-0 z-50 border-b border-white/60 bg-white/85 backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-4 py-3.5 sm:px-6 lg:px-8">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="group flex shrink-0 items-center gap-2.5">
            <span class="relative flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-hb-blue to-hb-blue-deeper font-display text-lg font-bold text-hb-gold shadow-md transition group-hover:shadow-glow">
                HB
            </span>
            <span class="font-display text-xl font-bold tracking-tight text-hb-blue-deeper">
                Hôtels <span class="text-hb-orange">Bénin</span>
            </span>
        </a>

        {{-- Nav desktop --}}
        <nav class="hidden items-center gap-1 lg:flex">
            <a href="{{ route('home') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('home') ? 'bg-hb-cream text-hb-blue' : '' }}">Accueil</a>

            {{-- Dropdown Établissements --}}
            <div class="relative" @mouseenter="dropdownOpen = true" @mouseleave="dropdownOpen = false">
                <a href="{{ route('establishments.index') }}"
                   class="inline-flex items-center gap-1 rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('establishments.*') ? 'bg-hb-cream text-hb-blue' : '' }}">
                    Établissements
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transition" :class="dropdownOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                </a>

                <div x-cloak x-show="dropdownOpen" x-transition.opacity x-transition.scale.origin.top
                     class="absolute left-0 z-50 mt-2 w-72 overflow-hidden rounded-2xl border border-gray-100 bg-white p-2 shadow-card-lg">
                    <a href="{{ route('establishments.index') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue">
                        <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-hb-blue/10 text-hb-blue"><i class="bi bi-grid"></i></span>
                        Tous les établissements
                    </a>
                    <div class="my-1 h-px bg-gray-100"></div>
                    @foreach($headerTypes as $type)
                        <a href="{{ route('establishments.index', ['type' => $type->slug]) }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue">
                            <span class="flex h-9 w-9 items-center justify-center rounded-lg bg-hb-orange/10 text-hb-orange"><i class="bi bi-{{ $type->icon }}"></i></span>
                            {{ $type->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('about') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('about') ? 'bg-hb-cream text-hb-blue' : '' }}">À propos</a>
            <a href="{{ route('contact.create') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('contact.*') ? 'bg-hb-cream text-hb-blue' : '' }}">Contact</a>
        </nav>

        {{-- Actions desktop côté droit --}}
        <div class="hidden items-center gap-2 lg:flex">
            {{-- Recherche toggle --}}
            <button @click="searchOpen = !searchOpen" class="inline-flex h-10 w-10 items-center justify-center rounded-full text-hb-blue transition hover:bg-hb-cream" aria-label="Rechercher">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
            <a href="{{ route('login.show') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-hb-blue transition hover:bg-hb-cream">Connexion</a>
            <a href="{{ route('register.show') }}" class="btn-gold !px-5 !py-2.5 text-sm">Devenir hôte</a>
        </div>

        {{-- Recherche mobile + toggle --}}
        <div class="flex items-center gap-1 lg:hidden">
            <button @click="searchOpen = !searchOpen" class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-hb-blue-deeper transition hover:bg-hb-cream" aria-label="Rechercher">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            </button>
            <button @click="mobileOpen = !mobileOpen" class="inline-flex h-11 w-11 items-center justify-center rounded-xl text-hb-blue-deeper transition hover:bg-hb-cream" aria-label="Ouvrir le menu" :aria-expanded="mobileOpen">
                <svg x-show="!mobileOpen" xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
                <svg x-show="mobileOpen" x-cloak xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>
    </div>

    {{-- Barre de recherche dépliante --}}
    <div x-cloak x-show="searchOpen" x-transition.opacity
         class="border-t border-gray-100 bg-white/95 px-4 py-4 backdrop-blur">
        <form action="{{ route('establishments.index') }}" method="GET" class="mx-auto grid max-w-5xl grid-cols-1 gap-3 sm:grid-cols-3">
            <div class="relative">
                <i class="bi bi-geo-alt pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-hb-orange"></i>
                <select name="locality" class="input-field !pl-9">
                    <option value="">Toutes les villes</option>
                    @foreach($headerLocalities as $locality)
                        <option value="{{ $locality->slug }}">{{ $locality->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="relative">
                <i class="bi bi-building pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-hb-orange"></i>
                <select name="type" class="input-field !pl-9">
                    <option value="">Tous les types</option>
                    @foreach($headerTypes as $type)
                        <option value="{{ $type->slug }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn-primary">Rechercher</button>
        </form>
    </div>

    {{-- Menu mobile --}}
    <div x-cloak x-show="mobileOpen" x-transition.opacity
         class="border-t border-gray-100 bg-white lg:hidden">
        <nav class="flex flex-col gap-1 px-4 py-4">
            <a href="{{ route('home') }}" @click="mobileOpen = false" class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('home') ? 'bg-hb-cream text-hb-blue' : '' }}">Accueil</a>

            <p class="px-4 pt-2 text-xs font-bold uppercase tracking-wider text-gray-400">Établissements</p>
            <a href="{{ route('establishments.index') }}" @click="mobileOpen = false" class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue">Tous les établissements</a>
            @foreach($headerTypes as $type)
                <a href="{{ route('establishments.index', ['type' => $type->slug]) }}" @click="mobileOpen = false" class="flex items-center gap-2 rounded-xl px-4 py-2.5 text-sm text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue">
                    <i class="bi bi-{{ $type->icon }} text-hb-orange"></i> {{ $type->name }}
                </a>
            @endforeach

            <div class="my-2 h-px bg-gray-100"></div>
            <a href="{{ route('about') }}" @click="mobileOpen = false" class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue">À propos</a>
            <a href="{{ route('contact.create') }}" @click="mobileOpen = false" class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue">Contact</a>
            <div class="my-2 h-px bg-gray-100"></div>
            <a href="{{ route('login.show') }}" class="rounded-xl px-4 py-3 text-sm font-semibold text-hb-blue transition hover:bg-hb-cream">Connexion</a>
            <a href="{{ route('register.show') }}" @click="mobileOpen = false" class="btn-gold mt-1">Devenir hôte</a>
        </nav>
    </div>
</header>
