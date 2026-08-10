{{-- Alpine.js --}}
<style>
    [x-cloak] {
        display: none !important;
    }
</style>

<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>


<header
    x-data="{ mobileOpen: false, searchOpen: false }"
    class="sticky top-0 z-50 border-b border-white/60 bg-white/85 backdrop-blur-md"
>
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


        {{-- =========================================================
             NAVIGATION DESKTOP
        ========================================================== --}}
        <nav class="hidden items-center gap-1 lg:flex">

            <a
                href="{{ route('home') }}"
                class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('home') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                Accueil
            </a>

            <a
                href="{{ route('establishments.index') }}"
                class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('establishments.*') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                Établissements
            </a>

            <a
                href="{{ route('about') }}"
                class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('about') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                À propos
            </a>

            <a
                href="{{ route('events.index') }}"
                class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('events.*') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                Événements
            </a>

            <a
                href="{{ route('contact.create') }}"
                class="rounded-full px-4 py-2 text-sm font-semibold text-gray-600 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('contact.*') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                Contact
            </a>

        </nav>


        {{-- =========================================================
             ACTIONS DESKTOP
        ========================================================== --}}
        <div class="hidden items-center gap-2 lg:flex">

            {{-- Recherche --}}
            <button
                @click="searchOpen = !searchOpen"
                class="inline-flex h-10 w-10 items-center justify-center rounded-full text-hb-blue transition hover:bg-hb-cream"
                aria-label="Rechercher"
                :aria-expanded="searchOpen"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-5 w-5"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
            </button>

            @auth

                <a
                    href="{{ auth()->user()->isAdmin()
                        ? route('admin.dashboard')
                        : (auth()->user()->isHote()
                            ? route('hote.dashboard')
                            : route('reservations.dashboard')) }}"
                    class="rounded-full px-4 py-2 text-sm font-semibold text-hb-blue transition hover:bg-hb-cream"
                >
                    Mon espace
                </a>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf

                    <button
                        type="submit"
                        class="btn-outline-blue !px-5 !py-2.5 text-sm"
                    >
                        Déconnexion
                    </button>
                </form>

            @else

                <a
                    href="{{ route('login.show') }}"
                    class="rounded-full px-4 py-2 text-sm font-semibold text-hb-blue transition hover:bg-hb-cream"
                >
                    Connexion
                </a>

                <a
                    href="{{ route('register.show') }}"
                    class="btn-gold !px-5 !py-2.5 text-sm"
                >
                    Devenir hôte
                </a>

            @endauth

        </div>


        {{-- =========================================================
             ACTIONS MOBILE
        ========================================================== --}}
        <div class="flex items-center gap-1 lg:hidden">

            {{-- Bouton recherche mobile --}}
            <button
                @click="searchOpen = !searchOpen"
                class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-hb-blue-deeper transition hover:bg-hb-cream"
                aria-label="Rechercher"
                :aria-expanded="searchOpen"
            >
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-6 w-6"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 21l-4.35-4.35M17 10a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>
            </button>


            {{-- Bouton menu mobile --}}
            <button
                @click="mobileOpen = !mobileOpen"
                class="inline-flex h-11 w-11 items-center justify-center rounded-xl text-hb-blue-deeper transition hover:bg-hb-cream"
                aria-label="Ouvrir le menu"
                :aria-expanded="mobileOpen"
            >

                {{-- Icône hamburger --}}
                <svg
                    x-cloak
                    x-show="!mobileOpen"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M4 6h16M4 12h16M4 18h16"
                    />
                </svg>

                {{-- Icône fermeture --}}
                <svg
                    x-cloak
                    x-show="mobileOpen"
                    xmlns="http://www.w3.org/2000/svg"
                    class="h-7 w-7"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12"
                    />
                </svg>

            </button>

        </div>

    </div>


    {{-- =========================================================
         BARRE DE RECHERCHE
    ========================================================== --}}
    <div
        x-cloak
        x-show="searchOpen"
        x-transition.opacity
        class="border-t border-gray-100 bg-white/95 px-4 py-4 backdrop-blur"
    >
        <form
            action="{{ route('establishments.index') }}"
            method="GET"
            class="mx-auto grid max-w-5xl grid-cols-1 gap-3 sm:grid-cols-3"
        >

            {{-- Ville --}}
            <div class="relative">

                <i
                    class="bi bi-geo-alt pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-hb-orange"
                ></i>

                <select
                    name="locality"
                    class="input-field !pl-9"
                >
                    <option value="">Toutes les villes</option>

                    @foreach($headerLocalities as $locality)
                        <option value="{{ $locality->slug }}">
                            {{ $locality->name }}
                        </option>
                    @endforeach
                </select>

            </div>


            {{-- Type --}}
            <div class="relative">

                <i
                    class="bi bi-building pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-hb-orange"
                ></i>

                <select
                    name="type"
                    class="input-field !pl-9"
                >
                    <option value="">Tous les types</option>

                    @foreach($headerTypes as $type)
                        <option value="{{ $type->slug }}">
                            {{ $type->name }}
                        </option>
                    @endforeach
                </select>

            </div>


            {{-- Bouton recherche --}}
            <button
                type="submit"
                class="btn-primary"
            >
                Rechercher
            </button>

        </form>
    </div>


    {{-- =========================================================
         MENU MOBILE
    ========================================================== --}}
    <div
        x-cloak
        x-show="mobileOpen"
        x-transition.opacity
        class="border-t border-gray-100 bg-white lg:hidden"
    >

        <nav class="flex flex-col gap-1 px-4 py-4">

            {{-- Accueil --}}
            <a
                href="{{ route('home') }}"
                @click="mobileOpen = false"
                class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('home') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                Accueil
            </a>


            {{-- Établissements --}}
            <a
                href="{{ route('establishments.index') }}"
                @click="mobileOpen = false"
                class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('establishments.*') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                Établissements
            </a>


            {{-- À propos --}}
            <a
                href="{{ route('about') }}"
                @click="mobileOpen = false"
                class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('about') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                À propos
            </a>


            {{-- Événements --}}
            <a
                href="{{ route('events.index') }}"
                @click="mobileOpen = false"
                class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('events.*') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                Événements
            </a>


            {{-- Contact --}}
            <a
                href="{{ route('contact.create') }}"
                @click="mobileOpen = false"
                class="rounded-xl px-4 py-3 text-sm font-semibold text-gray-700 transition hover:bg-hb-cream hover:text-hb-blue {{ request()->routeIs('contact.*') ? 'bg-hb-cream text-hb-blue' : '' }}"
            >
                Contact
            </a>


            {{-- Séparateur --}}
            <div class="my-2 h-px bg-gray-100"></div>


            @auth

                {{-- Mon espace --}}
                <a
                    href="{{ auth()->user()->isAdmin()
                        ? route('admin.dashboard')
                        : (auth()->user()->isHote()
                            ? route('hote.dashboard')
                            : route('reservations.dashboard')) }}"
                    @click="mobileOpen = false"
                    class="rounded-xl px-4 py-3 text-sm font-semibold text-hb-blue transition hover:bg-hb-cream"
                >
                    Mon espace
                </a>


                {{-- Déconnexion --}}
                <form
                    action="{{ route('logout') }}"
                    method="POST"
                    class="mt-1"
                >
                    @csrf

                    <button
                        type="submit"
                        class="btn-outline-blue w-full"
                    >
                        Déconnexion
                    </button>
                </form>

            @else

                {{-- Connexion --}}
                <a
                    href="{{ route('login.show') }}"
                    @click="mobileOpen = false"
                    class="rounded-xl px-4 py-3 text-sm font-semibold text-hb-blue transition hover:bg-hb-cream"
                >
                    Connexion
                </a>


                {{-- Devenir hôte --}}
                <a
                    href="{{ route('register.show') }}"
                    @click="mobileOpen = false"
                    class="btn-gold mt-1"
                >
                    Devenir hôte
                </a>

            @endauth

        </nav>

    </div>

</header>