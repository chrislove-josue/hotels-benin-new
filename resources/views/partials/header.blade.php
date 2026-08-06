<header x-data="{ mobileOpen: false, dropdownOpen: false }"
        class="sticky top-0 z-50 border-b border-white/60 bg-white/85 backdrop-blur-md">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3.5 sm:px-6 lg:px-8">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="group flex items-center gap-2.5">
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
            <a href="{{ route('establishments.index') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('establishments.*') ? 'bg-hb-cream text-hb-blue' : '' }}">Établissements</a>
            <a href="{{ route('about') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('about') ? 'bg-hb-cream text-hb-blue' : '' }}">À propos</a>
            <a href="{{ route('contact.create') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('contact.*') ? 'bg-hb-cream text-hb-blue' : '' }}">Contact</a>
        </nav>

        {{-- Actions desktop --}}
        <div class="hidden items-center gap-2 lg:flex">
            <a href="{{ route('login.show') }}" class="rounded-full px-4 py-2 text-sm font-semibold text-hb-blue transition hover:bg-hb-cream">Connexion</a>
            <a href="{{ route('register.show') }}" class="btn-gold !px-5 !py-2.5 text-sm">S'inscrire</a>
        </div>

        {{-- Toggle mobile --}}
<button @click="mobileOpen = !mobileOpen" class="inline-flex h-11 w-11 items-center justify-center rounded-xl text-hb-blue-deeper transition hover:bg-hb-cream lg:hidden" aria-label="Ouvrir le menu" :aria-expanded="mobileOpen">
            <i x-show="!mobileOpen" class="bi bi-list text-3xl"></i>
            <i x-show="mobileOpen" x-cloak class="bi bi-x-lg text-2xl"></i>
        </button>
    </div>

    {{-- Menu mobile --}}
    <div x-cloak x-show="mobileOpen" x-transition.opacity
         class="border-t border-gray-100 bg-white lg:hidden">
        <nav class="flex flex-col gap-1 px-4 py-4">
            <a href="{{ route('home') }}" @click="mobileOpen = false" class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('home') ? 'bg-hb-cream text-hb-blue' : '' }}">Accueil</a>
            <a href="{{ route('establishments.index') }}" @click="mobileOpen = false" class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('establishments.*') ? 'bg-hb-cream text-hb-blue' : '' }}">Établissements</a>
            <a href="{{ route('about') }}" @click="mobileOpen = false" class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('about') ? 'bg-hb-cream text-hb-blue' : '' }}">À propos</a>
            <a href="{{ route('contact.create') }}" @click="mobileOpen = false" class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('contact.*') ? 'bg-hb-cream text-hb-blue' : '' }}">Contact</a>
            <div class="my-2 h-px bg-gray-100"></div>
            <a href="{{ route('login.show') }}" class="rounded-xl px-4 py-3 text-sm font-semibold text-hb-blue transition hover:bg-hb-cream">Connexion</a>
            <a href="{{ route('register.show') }}" class="btn-gold mt-1">S'inscrire</a>
        </nav>
    </div>
</header>
