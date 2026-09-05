

<?php $__env->startSection('title', 'Hôtels Bénin — Trouvez votre séjour idéal'); ?>

<?php $__env->startSection('content'); ?>


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

            
            <form action="<?php echo e(route('establishments.index')); ?>" method="GET" class="mt-10 grid gap-3 rounded-3xl bg-white/95 p-4 shadow-card-lg backdrop-blur sm:grid-cols-4">
                <div class="sm:col-span-1">
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Localité</label>
                    <select name="locality" class="input-field">
                        <option value="">Toutes les villes</option>
                        <?php $__currentLoopData = $localities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($locality->slug); ?>"><?php echo e($locality->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="sm:col-span-1">
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Type d'établissement</label>
                    <select name="type" class="input-field">
                        <option value="">Tous les types</option>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->slug); ?>"><?php echo e($type->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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


    <div class="relative border-t border-white/10 bg-white/5 backdrop-blur">
        <div class="mx-auto grid max-w-7xl grid-cols-2 gap-6 px-4 py-8 sm:px-6 md:grid-cols-4 lg:px-8"
             x-data="stats()" x-init="init">
            <?php
                $stats = [
                    ['value' => max(8, $localities->count()), 'suffix' => '+', 'label' => 'Villes couvertes', 'decimals' => 0],
                    ['value' => max(13, $featured->count() + $topRated->count()), 'suffix' => '+', 'label' => 'Établissements', 'decimals' => 0],
                    ['value' => 4.8, 'suffix' => '/5', 'label' => 'Note moyenne', 'decimals' => 1],
                    ['value' => 24, 'suffix' => 'h', 'label' => 'Réponse support', 'decimals' => 0],
                ];
            ?>
            <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="text-center" data-reveal>
                    <p class="font-display text-3xl font-bold text-hb-gold">
                        <span x-ref="val<?php echo e($i); ?>" x-text="display(<?php echo e($i); ?>)"></span><span class="text-hb-orange"><?php echo e($stat['suffix']); ?></span>
                    </p>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-wider text-white/60"><?php echo e($stat['label']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <script>
        function stats() {
            return {
                values: <?php echo json_encode(array_column($stats, 'value'), 512) ?>,
                decimals: <?php echo json_encode(array_column($stats, 'decimals'), 512) ?>,
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
                            const current = target * eased;
                            el.textContent = this.decimals[i] > 0
                                ? current.toFixed(this.decimals[i])
                                : Math.round(current);
                            if (p < 1) requestAnimationFrame(step);
                        };
                        requestAnimationFrame(step);
                    });
                },
                display(i) {
                    return this.decimals[i] > 0 ? this.values[i].toFixed(this.decimals[i]) : this.values[i];
                },
            };
        }
    </script>
</section>


<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex flex-col items-start gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <span class="section-eyebrow">Destinations</span>
            <h2 class="section-title">Explorez le Bénin par ville</h2>
            <div class="divider-gold"></div>
        </div>
<a href="<?php echo e(route('establishments.index')); ?>" class="btn-outline-blue !px-5 !py-2.5 text-sm">Voir toutes les villes <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="grid grid-cols-2 gap-4 md:grid-cols-4" data-reveal>
        <?php $__currentLoopData = $localities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('establishments.index', ['locality' => $locality->slug])); ?>"
               class="card group flex flex-col items-center justify-center gap-2 p-6 text-center">
<i class="bi bi-geo-alt text-3xl text-hb-orange transition duration-300 group-hover:scale-125 group-hover:-translate-y-1"></i>
                <span class="font-display font-bold text-hb-blue group-hover:text-hb-orange"><?php echo e($locality->name); ?></span>
                <span class="text-xs text-gray-400"><?php echo e($locality->establishments_count); ?> établissement(s)</span>
                <span class="mt-1 h-0.5 w-0 bg-gradient-to-r from-hb-gold to-hb-orange transition-all duration-300 group-hover:w-10"></span>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>


<section class="bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center">
            <span class="section-eyebrow justify-center">Simple & rapide</span>
            <h2 class="section-title">Comment ça marche ?</h2>
            <div class="divider-gold mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 gap-8 md:grid-cols-3" data-reveal>
            <?php
                $steps = [
                    ['icon' => 'bi-search', 'num' => '01', 'title' => 'Recherchez', 'desc' => 'Explorez les hôtels, résidences et salles de fête par ville, type ou budget.'],
                    ['icon' => 'bi-calendar-check', 'num' => '02', 'title' => 'Choisissez', 'desc' => 'Comparez les tarifs, lisez les avis et sélectionnez l\'établissement qui vous convient.'],
                    ['icon' => 'bi-key', 'num' => '03', 'title' => 'Réservez', 'desc' => 'Confirmez votre séjour en toute confiance et profitez de votre voyage au Bénin.'],
                ];
            ?>
            <?php $__currentLoopData = $steps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $step): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="relative rounded-2xl bg-hb-cream p-8 text-center transition duration-300 hover:-translate-y-1 hover:shadow-card-lg">
                    <span class="absolute right-6 top-4 font-display text-5xl font-bold text-hb-gold/20"><?php echo e($step['num']); ?></span>
                    <span class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-hb-blue to-hb-blue-deeper text-hb-gold shadow-md">
                        <i class="bi <?php echo e($step['icon']); ?> text-2xl"></i>
                    </span>
                    <h3 class="font-display text-xl font-bold text-hb-blue-deeper"><?php echo e($step['title']); ?></h3>
                    <p class="mt-2 text-sm leading-relaxed text-gray-600"><?php echo e($step['desc']); ?></p>

                    <?php if($i < count($steps) - 1): ?>
                        <span class="absolute -right-4 top-1/2 hidden -translate-y-1/2 text-hb-gold md:block"><i class="bi bi-arrow-right-circle-fill text-xl"></i></span>
                    <?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="relative overflow-hidden bg-hb-blue-deeper py-16">
    <div class="pointer-events-none absolute -left-24 top-0 h-80 w-80 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -right-24 bottom-0 h-80 w-80 rounded-full bg-hb-orange/20 blur-3xl"></div>

    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-12 text-center">
            <span class="section-eyebrow justify-center text-hb-gold">Nos atouts</span>
            <h2 class="font-display text-3xl font-bold text-white md:text-4xl">Pourquoi choisir Hôtels Bénin ?</h2>
            <div class="divider-gold mx-auto"></div>
        </div>

        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
            <?php
                $features = [
                    ['icon' => 'bi-shield-check', 'title' => 'Sélection fiable', 'desc' => 'Des établissements vérifiés et notés par nos voyageurs.'],
                    ['icon' => 'bi-cash-coin', 'title' => 'Prix transparents', 'desc' => 'Des tarifs clairs en FCFA, sans frais cachés.'],
                    ['icon' => 'bi-headset', 'title' => 'Support 24h/7', 'desc' => 'Une équipe à Cotonou pour vous accompagner.'],
                    ['icon' => 'bi-map', 'title' => 'Tout le Bénin', 'desc' => 'De Cotonou à Natitingou, les meilleures adresses.'],
                ];
            ?>
            <?php $__currentLoopData = $features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feature): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="glass p-6 text-center transition duration-300 hover:-translate-y-1 hover:border-hb-gold/50">
                    <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-hb-gold to-hb-orange text-hb-blue-deeper shadow-md">
                        <i class="bi <?php echo e($feature['icon']); ?> text-2xl"></i>
                    </span>
                    <h3 class="font-display text-lg font-bold text-white"><?php echo e($feature['title']); ?></h3>
                    <p class="mt-2 text-sm text-white/70"><?php echo e($feature['desc']); ?></p>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>


<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex flex-col items-start gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <span class="section-eyebrow">Catégories</span>
            <h2 class="section-title">Trouvez le type d'hébergement idéal</h2>
            <div class="divider-gold"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3" data-reveal>
        <?php
            $catImages = [
                'hotel' => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
                'residence-meublee' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
                'salle-de-fete' => 'https://images.unsplash.com/photo-1519167758481-83f550bb49b3?w=800&q=80',
                'auberge' => 'https://images.unsplash.com/photo-1590490360182-c33d57733427?w=800&q=80',
                'villa' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=800&q=80',
            ];
        ?>
        <?php $__currentLoopData = $headerTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('establishments.index', ['type' => $type->slug])); ?>"
               class="group relative h-64 overflow-hidden rounded-2xl shadow-card">
                <img src="<?php echo e($catImages[$type->slug] ?? 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80'); ?>"
                     alt="<?php echo e($type->name); ?>" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy">
                <div class="absolute inset-0 bg-gradient-to-t from-hb-blue-deeper/90 via-hb-blue-deeper/20 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 flex items-center justify-between p-5">
                    <div>
                        <span class="mb-1 flex h-10 w-10 items-center justify-center rounded-full bg-white/20 text-hb-gold backdrop-blur"><i class="bi bi-<?php echo e($type->icon); ?>"></i></span>
                        <h3 class="font-display text-xl font-bold text-white"><?php echo e($type->name); ?></h3>
                    </div>
                    <span class="flex h-10 w-10 items-center justify-center rounded-full bg-hb-gold text-hb-blue-deeper opacity-0 transition duration-300 group-hover:opacity-100 group-hover:translate-x-0 translate-x-4">
                        <i class="bi bi-arrow-right"></i>
                    </span>
                </div>
            </a>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>


<?php if($featured->isNotEmpty()): ?>
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
            <?php $__currentLoopData = $featured; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $establishment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('partials.establishment-card', ['establishment' => $establishment], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
</section>
<?php endif; ?>


<section class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="mb-10 flex flex-col items-start gap-4 md:flex-row md:items-end md:justify-between">
        <div>
            <span class="section-eyebrow">Sélection</span>
            <h2 class="section-title">Les mieux notés en ce moment</h2>
            <div class="divider-gold"></div>
        </div>
<a href="<?php echo e(route('establishments.index')); ?>" class="btn-outline-blue !px-5 !py-2.5 text-sm">Voir tous les établissements <i class="bi bi-arrow-right"></i></a>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4" data-reveal>
        <?php $__currentLoopData = $topRated; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $establishment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php echo $__env->make('partials.establishment-card', ['establishment' => $establishment, 'compact' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>


<section class="bg-hb-sand py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"
         x-data="testimonials()">
        <div class="mb-12 text-center">
            <span class="section-eyebrow justify-center">Ils nous font confiance</span>
            <h2 class="section-title">Ce que disent nos voyageurs</h2>
            <div class="divider-gold mx-auto"></div>
        </div>

<div class="relative mx-auto max-w-3xl" @mouseenter="stop()" @mouseleave="start()">
            <div class="overflow-hidden">
                <div class="flex transition-transform duration-500 ease-out" :style="'transform: translateX(-' + (slide * 100) + '%)'">
                    <?php
                        $testimonials = [
                            ['name' => 'Aïcha K.', 'city' => 'Cotonou', 'text' => 'J\'ai trouvé une superbe résidence à Cotonou en quelques minutes. Le site est clair et les photos sont fidèles à la réalité.', 'rating' => 5],
                            ['name' => 'Jean-Marc D.', 'city' => 'Paris', 'text' => 'Très pratique pour préparer un voyage d\'affaires au Bénin. Les informations sur les hôtels sont complètes et fiables.', 'rating' => 5],
                            ['name' => 'Fatou S.', 'city' => 'Natitingou', 'text' => 'En tant que propriétaire, j\'ai reçu de nombreuses demandes grâce à Hôtels Bénin. Une excellente vitrine pour mon établissement.', 'rating' => 4],
                            ['name' => 'Kofi A.', 'city' => 'Accra', 'text' => 'La recherche par budget et par ville est très pratique. J\'ai réussi à trouver une salle de fête parfaite pour mon événement.', 'rating' => 5],
                        ];
                    ?>
                    <?php $__currentLoopData = $testimonials; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="w-full shrink-0 px-2">
                            <div class="card p-8 text-center">
                                <div class="mb-4 flex justify-center gap-1 text-hb-gold">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi <?php echo e($i <= $t['rating'] ? 'bi-star-fill' : 'bi-star'); ?>"></i>
                                    <?php endfor; ?>
                                </div>
                                <p class="mx-auto max-w-xl text-lg leading-relaxed text-gray-700">"<?php echo e($t['text']); ?>"</p>
                                <div class="mt-6 flex items-center justify-center gap-3">
                                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-gradient-to-br from-hb-blue to-hb-blue-deeper font-display font-bold text-hb-gold">
                                        <?php echo e(collect(explode(' ', $t['name']))->take(2)->map(fn($n) => $n[0])->join('')); ?>

                                    </span>
                                    <div class="text-left">
                                        <p class="font-semibold text-hb-blue-deeper"><?php echo e($t['name']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo e($t['city']); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>

            
            <div class="mt-8 flex items-center justify-center gap-4">
                <button @click="prev()" class="flex h-11 w-11 items-center justify-center rounded-full border border-gray-300 text-hb-blue transition hover:bg-hb-blue hover:text-white" aria-label="Précédent">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <div class="flex gap-2">
                    <template x-for="(t, i) in items" :key="i">
                        <button @click="go(i)" class="h-2.5 rounded-full transition-all duration-300" :class="i === slide ? 'w-8 bg-hb-orange' : 'w-2.5 bg-gray-300'"></button>
                    </template>
                </div>
                <button @click="next()" class="flex h-11 w-11 items-center justify-center rounded-full border border-gray-300 text-hb-blue transition hover:bg-hb-blue hover:text-white" aria-label="Suivant">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>

<script>
        function testimonials() {
            return {
                slide: 0,
                items: <?php echo json_encode($testimonials, 15, 512) ?>,
                timer: null,
                init() {
                    this.start();
                },
                start() {
                    this.stop();
                    this.timer = setInterval(() => this.next(), 5000);
                },
                stop() {
                    if (this.timer) clearInterval(this.timer);
                },
                next() { this.slide = (this.slide + 1) % this.items.length; },
                prev() { this.slide = (this.slide - 1 + this.items.length) % this.items.length; },
                go(i) { this.slide = i; this.start(); },
            };
        }
    </script>
</section>


<section class="relative overflow-hidden bg-hb-blue-deeper py-16">
    <div class="pointer-events-none absolute -right-24 top-0 h-80 w-80 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-80 w-80 rounded-full bg-hb-orange/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-4xl px-4 text-center sm:px-6 lg:px-8" data-reveal>
        <span class="badge-gold-solid mb-4">Devenir hôte</span>
        <h2 class="font-display text-3xl font-bold text-white md:text-4xl">Vous gérez un hôtel ou une salle de fête ?</h2>
        <p class="mx-auto mt-4 mb-8 max-w-2xl text-white/75">
            Rejoignez Hôtels Bénin et faites découvrir votre établissement à des milliers de voyageurs.
        </p>
        <a href="<?php echo e(route('register.show')); ?>" class="btn-gold">Devenir hôte partenaire</a>
    </div>
</section>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/home.blade.php ENDPATH**/ ?>