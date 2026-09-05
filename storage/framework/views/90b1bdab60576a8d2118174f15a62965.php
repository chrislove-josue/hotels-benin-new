

<?php $__env->startSection('title', 'À propos — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="relative overflow-hidden bg-gradient-to-br from-hb-blue to-hb-blue-deeper py-20 text-center text-white">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-hb-orange/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-3xl px-4" data-reveal>
        <span class="badge-gold-solid mb-4">Notre histoire</span>
        <h1 class="font-display text-4xl font-bold md:text-5xl">À propos de Hôtels Bénin</h1>
        <div class="mx-auto mt-4 h-1 w-20 rounded-full bg-gradient-to-r from-hb-gold to-hb-orange"></div>
        <p class="mt-5 text-lg text-white/85">
            Nous connectons voyageurs et hôtes à travers tout le Bénin, pour rendre chaque séjour simple, sûr et mémorable.
        </p>
    </div>
</section>

<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-10 md:grid-cols-2 md:items-center" data-reveal>
        <div>
            <span class="section-eyebrow">Notre mission</span>
            <h2 class="section-title mb-4">Connecter voyageurs et hébergements béninois</h2>
            <div class="divider-gold"></div>
            <p class="mt-6 leading-relaxed text-gray-600">
                Hôtels Bénin est née d'un constat simple : il est souvent difficile de trouver, comparer et réserver un
                hébergement de qualité au Bénin. Notre équipe va directement à la rencontre des hôtels, résidences
                meublées, villas et salles de fête : nous démarchons ces établissements, vérifions leurs chambres et
                leurs espaces, et les rendons réservables en ligne. Les touristes et voyageurs accèdent ainsi à une
                sélection fiable d'hébergements et de salles pour tous leurs séjours et événements, avec des
                informations exactes et une réservation en quelques clics.
            </p>
        </div>
        <img src="https://images.unsplash.com/photo-1551882547-ff40c63fe5fa?w=900&q=80" alt="Équipe Hôtels Bénin" class="rounded-2xl shadow-card-lg">
    </div>

<div class="mt-20 grid grid-cols-1 gap-6 sm:grid-cols-3" data-reveal x-data="aboutStats()" x-init="init">
        <?php
            $aboutStats = [
                ['value' => 50, 'suffix' => '+', 'label' => 'Établissements partenaires'],
                ['value' => 10, 'suffix' => '', 'label' => 'Villes couvertes au Bénin'],
                ['value' => 100, 'suffix' => '%', 'label' => 'Équipe basée à Cotonou'],
            ];
        ?>
        <?php $__currentLoopData = $aboutStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="card p-6 text-center hover:-translate-y-1">
                <p class="bg-gradient-to-r from-hb-gold to-hb-orange bg-clip-text font-display text-4xl font-bold text-transparent">
                    <span x-ref="val<?php echo e($i); ?>">0</span><?php echo e($stat['suffix']); ?>

                </p>
                <p class="mt-2 text-sm text-gray-500"><?php echo e($stat['label']); ?></p>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <script>
            function aboutStats() {
                return {
                    values: <?php echo json_encode(array_column($aboutStats, 'value'), 512) ?>,
                    animated: false,
                    init() {
                        const io = new IntersectionObserver((entries) => {
                            entries.forEach((entry) => {
                                if (entry.isIntersecting && !this.animated) {
                                    this.animated = true;
                                    this.animate();
                                    io.unobserve(entry.target);
                                }
                            });
                        }, { threshold: 0.3 });
                        io.observe(this.$el);
                    },
                    animate() {
                        this.values.forEach((target, i) => {
                            const el = this.$refs['val' + i];
                            const total = 1500;
                            const start = performance.now();
                            const step = (now) => {
                                const p = Math.min((now - start) / total, 1);
                                const eased = 1 - Math.pow(1 - p, 3);
                                el.textContent = Math.round(target * eased);
                                if (p < 1) requestAnimationFrame(step);
                            };
                            requestAnimationFrame(step);
                        });
                    },
                };
            }
        </script>
    </div>

    
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/about.blade.php ENDPATH**/ ?>