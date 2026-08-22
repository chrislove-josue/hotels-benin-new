<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Espace voyageur — Hôtels Bénin'); ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="<?php echo e(asset('storage/logo-or.png')); ?>" type="image/x-icon">

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'hb-blue': { DEFAULT: '#1A3A6B', dark: '#0F2547', deeper: '#0B1D38', light: '#2C5090' },
                        'hb-gold': { DEFAULT: '#D4AF37', light: '#E6CB6B', pale: '#F7ECD0' },
                        'hb-orange': { DEFAULT: '#F59A23', dark: '#D9820F' },
                        'hb-cream': '#FBF8F1',
                        'hb-sand': '#F5EFE3',
                    },
                    fontFamily: {
                        sans: ['Montserrat', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['Playfair Display', 'serif'],
                    },
                    boxShadow: {
                        card: '0 10px 30px -12px rgba(26, 58, 107, 0.25)',
                        'card-lg': '0 25px 50px -12px rgba(11, 29, 56, 0.30)',
                        'glow': '0 0 40px -8px rgba(212, 175, 55, 0.45)',
                    },
                    borderRadius: {
                        '4xl': '2rem',
                    },
                    keyframes: {
                        fadeUp: {
                            '0%': { opacity: '0', transform: 'translateY(24px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' },
                        },
                    },
                    animation: {
                        fadeUp: 'fadeUp 0.7s ease-out both',
                    },
                },
            },
        }
    </script>

    <style type="text/tailwindcss">
        @layer base {
            body { @apply bg-hb-cream text-gray-800 antialiased; }
            ::selection { @apply bg-hb-gold/40 text-hb-blue-deeper; }
        }
        @layer components {
            .font-display { font-family: 'Playfair Display', serif; }
            .btn { @apply inline-flex items-center justify-center gap-2 rounded-full px-5 py-2.5 text-sm font-semibold transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-hb-gold focus-visible:ring-offset-2; }
            .btn-primary { @apply btn bg-hb-orange text-white shadow-md shadow-hb-orange/30 hover:-translate-y-0.5 hover:bg-hb-orange-dark hover:shadow-lg hover:shadow-hb-orange/40; }
            .btn-gold { @apply btn bg-gradient-to-r from-hb-gold to-hb-gold-light text-hb-blue-deeper shadow-md shadow-hb-gold/30 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-hb-gold/50; }
            .btn-outline { @apply btn border-2 border-hb-blue/20 bg-white text-hb-blue hover:border-hb-gold hover:text-hb-gold; }
            .btn-outline-blue { @apply btn border-2 border-hb-blue text-hb-blue hover:bg-hb-blue hover:text-white; }
            .btn-white { @apply btn bg-white text-hb-blue-deeper shadow-md hover:-translate-y-0.5 hover:shadow-lg; }
            .card { @apply overflow-hidden rounded-2xl bg-white shadow-card transition duration-300 hover:-translate-y-1 hover:shadow-card-lg; }
            .input-field { @apply w-full rounded-xl border border-gray-300 bg-white px-4 py-2.5 text-sm focus:border-hb-blue focus:outline-none focus:ring-2 focus:ring-hb-blue/20; }
            .label { @apply mb-1 block text-xs font-semibold text-gray-500; }
            .stat-card { @apply card p-5 flex items-center gap-4; }
            .badge-pill { @apply inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold; }
        }
    </style>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="min-h-screen font-sans">

<div x-data="{ sidebarOpen: false }" class="min-h-screen lg:flex">

    
    <aside class="fixed inset-y-0 left-0 z-40 w-72 -translate-x-full transform bg-hb-blue-deeper text-white transition lg:sticky lg:top-0 lg:h-screen lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
        <div class="flex h-full flex-col">
            <a href="<?php echo e(route('home')); ?>" class="flex items-center gap-2.5 border-b border-white/10 px-6 py-5">
<img src="<?php echo e(asset('storage/logo-or.png')); ?>" alt="Hôtels Bénin"
         class="h-16 w-16 rounded-full">
                <div>
                    <p class="font-display text-lg font-bold leading-none">Hôtels Bénin</p>
                    <p class="mt-1 text-[11px] uppercase tracking-wider text-hb-gold">Espace voyageur</p>
                </div>
            </a>

            <nav class="flex-1 space-y-1 px-3 py-4">
                <a href="<?php echo e(route('reservations.dashboard')); ?>" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition <?php echo e(request()->routeIs('reservations.dashboard') ? 'bg-hb-gold text-hb-blue-deeper font-semibold' : 'text-white/80 hover:bg-white/10'); ?>">
                    <i class="bi bi-speedometer2 text-lg"></i> Tableau de bord
                </a>
                <a href="<?php echo e(route('reservations.index')); ?>" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition <?php echo e(request()->routeIs('reservations.index') ? 'bg-hb-gold text-hb-blue-deeper font-semibold' : 'text-white/80 hover:bg-white/10'); ?>">
                    <i class="bi bi-calendar-check text-lg"></i> Mes réservations
                </a>
                <a href="<?php echo e(route('establishments.index')); ?>" class="flex items-center gap-3 rounded-xl px-4 py-2.5 text-sm transition hover:bg-white/10">
                    <i class="bi bi-search text-lg"></i> Rechercher
                </a>
            </nav>

            <div class="border-t border-white/10 p-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-hb-gold font-bold text-hb-blue-deeper"><?php echo e(strtoupper(substr(auth()->user()->name, 0, 1))); ?></div>
                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm font-semibold"><?php echo e(auth()->user()->name); ?></p>
                        <p class="text-xs text-white/50"><?php echo e(auth()->user()->email); ?></p>
                    </div>
                </div>
                <a href="<?php echo e(route('home')); ?>" class="mt-3 flex items-center justify-center gap-1 text-center text-xs font-semibold text-hb-gold hover:underline"><i class="bi bi-arrow-left"></i> Retour au site</a>
            </div>
        </div>
    </aside>

    
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/40 lg:hidden"></div>

    
    <div class="min-w-0 flex-1">
        
        <header class="sticky top-0 z-20 flex items-center justify-between border-b border-gray-200 bg-white/90 px-4 py-3 backdrop-blur sm:px-6">
            <button @click="sidebarOpen = !sidebarOpen" class="inline-flex h-10 w-10 items-center justify-center rounded-xl text-hb-blue hover:bg-hb-cream lg:hidden" aria-label="Menu">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" /></svg>
            </button>

            <div class="flex-1"></div>

            <a href="<?php echo e(route('establishments.index')); ?>" class="rounded-full px-4 py-2 text-sm font-semibold text-hb-blue transition hover:bg-hb-cream">
                <i class="bi bi-plus-circle"></i> Réserver
            </a>

            <form action="<?php echo e(route('logout')); ?>" method="POST" class="ml-2">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn-outline !px-4 !py-2 text-xs">Déconnexion</button>
            </form>
        </header>

        <main class="p-4 sm:p-6">
            <?php if(session('impersonated_by')): ?>
                <div class="mb-6 flex flex-col gap-2 rounded-xl bg-hb-orange px-4 py-3 text-sm font-semibold text-white sm:flex-row sm:items-center sm:justify-between">
                    <span><i class="bi bi-incognito"></i> Session administrateur — vous naviguez en tant que <strong><?php echo e(auth()->user()->name); ?></strong> (voyageur). Les actions effectuées sont réelles.</span>
                    <form action="<?php echo e(route('impersonation.stop')); ?>" method="POST">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="rounded-lg bg-white/20 px-3 py-1.5 text-xs font-bold transition hover:bg-white/30">
                            <i class="bi bi-box-arrow-left"></i> Revenir à l'espace admin
                        </button>
                    </form>
                </div>
            <?php endif; ?>
            <?php if(session('status')): ?>
                <div class="mb-6 flex items-center gap-2 rounded-xl bg-green-50 px-4 py-3 text-sm font-medium text-green-700">
                    <i class="bi bi-check-circle-fill"></i> <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </main>
    </div>
</div>

</body>
</html>
<?php /**PATH E:\Projects\hotels-benin-new\resources\views/layouts/voyageur.blade.php ENDPATH**/ ?>