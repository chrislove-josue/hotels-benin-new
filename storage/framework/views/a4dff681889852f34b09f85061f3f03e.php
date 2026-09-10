<!DOCTYPE html>
<html lang="fr" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'Hôtels Bénin — Réservez votre séjour au Bénin'); ?></title>
    <meta name="description" content="<?php echo $__env->yieldContent('meta_description', 'Hôtels, résidences, salles de fête et auberges au Bénin. Comparez, choisissez et réservez votre prochain séjour en toute simplicité.'); ?>">

    <link rel="shortcut icon" href="<?php echo e(asset('storage/logo-or.png')); ?>" type="image/x-icon">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;600;700;800&family=Montserrat:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    
    <script src="https://cdn.tailwindcss.com"></script>

    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'hb-blue': {
                            DEFAULT: '#1A3A6B',
                            dark: '#0F2547',
                            deeper: '#0B1D38',
                            light: '#2C5090',
                        },
                        'hb-gold': {
                            DEFAULT: '#D4AF37',
                            light: '#E6CB6B',
                            pale: '#F7ECD0',
                        },
                        'hb-orange': {
                            DEFAULT: '#F59A23',
                            dark: '#D9820F',
                        },
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
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-12px)' },
                        },
                        shimmer: {
                            '0%': { backgroundPosition: '-200% 0' },
                            '100%': { backgroundPosition: '200% 0' },
                        },
                    },
                    animation: {
                        fadeUp: 'fadeUp 0.7s ease-out both',
                        float: 'float 6s ease-in-out infinite',
                        shimmer: 'shimmer 2.5s linear infinite',
                    },
                },
            },
        }
    </script>

    
    <style type="text/tailwindcss">
        @layer base {
            body {
                @apply bg-hb-cream text-gray-800 antialiased;
            }
            ::selection {
                @apply bg-hb-gold/40 text-hb-blue-deeper;
            }
        }

        @layer components {
            .font-display {
                font-family: 'Playfair Display', serif;
            }

            .btn {
                @apply inline-flex items-center justify-center gap-2 rounded-full px-6 py-3 text-sm font-semibold transition-all duration-300 focus:outline-none focus-visible:ring-2 focus-visible:ring-hb-gold focus-visible:ring-offset-2;
            }
            .btn-primary {
                @apply btn bg-hb-orange text-white shadow-md shadow-hb-orange/30 hover:-translate-y-0.5 hover:bg-hb-orange-dark hover:shadow-lg hover:shadow-hb-orange/40;
            }
            .btn-gold {
                @apply btn bg-gradient-to-r from-hb-gold to-hb-gold-light text-hb-blue-deeper shadow-md shadow-hb-gold/30 hover:-translate-y-0.5 hover:shadow-lg hover:shadow-hb-gold/50;
            }
            .btn-outline {
                @apply btn border-2 border-hb-blue/20 bg-white/10 text-white backdrop-blur hover:border-hb-gold hover:text-hb-gold;
            }
            .btn-outline-blue {
                @apply btn border-2 border-hb-blue text-hb-blue hover:bg-hb-blue hover:text-white;
            }
            .btn-white {
                @apply btn bg-white text-hb-blue-deeper shadow-md hover:-translate-y-0.5 hover:shadow-lg;
            }

            .badge {
                @apply inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-semibold;
            }
            .badge-gold {
                @apply badge bg-hb-gold/15 text-hb-blue;
            }
            .badge-gold-solid {
                @apply badge bg-gradient-to-r from-hb-gold to-hb-gold-light text-hb-blue-deeper shadow-sm;
            }
            .badge-white {
                @apply badge bg-white/95 text-hb-blue-deeper shadow-sm backdrop-blur;
            }

            .card {
                @apply overflow-hidden rounded-2xl bg-white shadow-card transition duration-300 hover:-translate-y-1 hover:shadow-card-lg;
            }
            .glass {
                @apply rounded-2xl border border-white/20 bg-white/10 backdrop-blur-md;
            }

            .input-field {
                @apply w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm text-gray-800 placeholder-gray-400 transition focus:border-hb-blue focus:outline-none focus:ring-2 focus:ring-hb-blue/20;
            }

            .section-eyebrow {
                @apply mb-3 inline-flex items-center gap-2 text-xs font-bold uppercase tracking-[0.2em] text-hb-orange;
            }
            .section-title {
                @apply font-display text-3xl font-bold text-hb-blue-deeper md:text-4xl;
            }
            .section-title-light {
                @apply font-display text-3xl font-bold text-white md:text-4xl;
            }

.divider-gold {
                @apply mt-4 h-1 w-16 rounded-full bg-gradient-to-r from-hb-gold to-hb-orange;
            }

            .animation-fade-up {
                animation: fadeUp 0.7s ease-out both;
            }
        }
    </style>

    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="flex min-h-screen flex-col font-sans">

    <?php echo $__env->make('partials.header', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="flex-1">
        <?php if(session('status')): ?>
            <div class="mx-auto mt-4 max-w-5xl rounded-xl bg-hb-blue/10 px-4 py-3 text-center text-sm font-medium text-hb-blue">
                <?php echo e(session('status')); ?>

            </div>
        <?php endif; ?>

        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <?php echo $__env->make('partials.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const els = document.querySelectorAll('[data-reveal]');
            const io = new IntersectionObserver((entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animation-fade-up');
                        io.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.12 });
            els.forEach((el) => io.observe(el));
        });
    </script>
</body>
</html>
<?php /**PATH C:\Project\hotels-benin-new\resources\views/layouts/app.blade.php ENDPATH**/ ?>