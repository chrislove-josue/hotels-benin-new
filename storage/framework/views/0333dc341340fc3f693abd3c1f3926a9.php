<?php $__env->startSection('title', 'Tous les établissements — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="relative overflow-hidden border-b border-white/60 bg-gradient-to-br from-hb-blue to-hb-blue-deeper py-12">
    <div class="pointer-events-none absolute -right-20 top-0 h-64 w-64 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8" data-reveal>
        <span class="badge-gold-solid mb-4">Catalogue</span>
        <h1 class="font-display text-4xl font-bold text-white">Tous nos établissements</h1>
        <div class="mt-3 h-1 w-16 rounded-full bg-gradient-to-r from-hb-gold to-hb-orange"></div>
        <p class="mt-3 text-white/80"><?php echo e($establishments->total()); ?> établissement(s) disponible(s) au Bénin</p>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8"
         x-data="{ filtersOpen: true }">
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-4">

        
        <aside class="lg:col-span-1">
            
<button @click="filtersOpen = !filtersOpen"
                    class="mb-4 flex w-full items-center justify-between rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm font-bold text-hb-blue lg:hidden">
                Filtrer les résultats
                <i class="bi bi-chevron-down transition" :class="filtersOpen ? 'rotate-180' : ''"></i>
            </button>

            <form action="<?php echo e(route('establishments.index')); ?>" method="GET"
                  class="card space-y-5 p-5" :class="filtersOpen ? 'block' : 'hidden'" x-show="filtersOpen" x-cloak>
                <h2 class="font-display text-lg font-bold text-hb-blue-deeper">Affiner votre recherche</h2>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Localité</label>
                    <select name="locality" class="input-field">
                        <option value="">Toutes les villes</option>
                        <?php $__currentLoopData = $localities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($locality->slug); ?>" <?php if(($filters['locality'] ?? '') === $locality->slug): echo 'selected'; endif; ?>><?php echo e($locality->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Type d'établissement</label>
                    <select name="type" class="input-field">
                        <option value="">Tous les types</option>
                        <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($type->slug); ?>" <?php if(($filters['type'] ?? '') === $type->slug): echo 'selected'; endif; ?>><?php echo e($type->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Prix min</label>
                        <input type="number" name="min_price" value="<?php echo e($filters['min_price'] ?? ''); ?>" class="input-field" placeholder="0">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Prix max</label>
                        <input type="number" name="max_price" value="<?php echo e($filters['max_price'] ?? ''); ?>" class="input-field" placeholder="200000">
                    </div>
                </div>

                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Capacité minimale</label>
                    <input type="number" name="capacity" value="<?php echo e($filters['capacity'] ?? ''); ?>" class="input-field" placeholder="Ex : 2">
                </div>

                <button type="submit" class="btn-primary w-full">Appliquer les filtres</button>
                <a href="<?php echo e(route('establishments.index')); ?>" class="block text-center text-sm font-semibold text-gray-500 hover:text-hb-blue">Réinitialiser</a>
            </form>
        </aside>

        
        <div class="lg:col-span-3">

            <div class="mb-6 flex items-center justify-between gap-3">
                <p class="text-sm text-gray-600">
                    <span class="font-bold text-hb-blue"><?php echo e($establishments->total()); ?></span> résultat(s)
                </p>
                <form method="GET" action="<?php echo e(route('establishments.index')); ?>" id="sort-form" class="flex items-center gap-2">
                    <?php $__currentLoopData = $filters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if($key !== 'sort' && $value !== '' && $value !== null): ?>
                            <input type="hidden" name="<?php echo e($key); ?>" value="<?php echo e($value); ?>">
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <label for="sort" class="text-xs font-semibold text-gray-500">Trier :</label>
                    <select id="sort" name="sort" @change="document.getElementById('sort-form').submit()" class="input-field !w-auto !py-2 text-sm">
                        <option value="latest" <?php if(($filters['sort'] ?? '') === 'latest'): echo 'selected'; endif; ?>>Plus récents</option>
                        <option value="rating" <?php if(($filters['sort'] ?? '') === 'rating'): echo 'selected'; endif; ?>>Mieux notés</option>
                        <option value="price_asc" <?php if(($filters['sort'] ?? '') === 'price_asc'): echo 'selected'; endif; ?>>Prix croissant</option>
                        <option value="price_desc" <?php if(($filters['sort'] ?? '') === 'price_desc'): echo 'selected'; endif; ?>>Prix décroissant</option>
                    </select>
                </form>
            </div>

            <?php if($establishments->isEmpty()): ?>
                <div class="card p-10 text-center">
                    <p class="text-lg font-semibold text-hb-blue-deeper">Aucun établissement ne correspond à ces critères.</p>
                    <p class="mt-2 text-sm text-gray-500">Essayez d'élargir votre recherche.</p>
                    <a href="<?php echo e(route('establishments.index')); ?>" class="btn-outline-blue mt-5 !px-5 !py-2.5 text-sm">Réinitialiser les filtres</a>
                </div>
            <?php else: ?>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 xl:grid-cols-3" data-reveal>
                    <?php $__currentLoopData = $establishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $establishment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php echo $__env->make('partials.establishment-card', ['establishment' => $establishment], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>

                <div class="mt-10">
                    <?php echo e($establishments->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/establishments/index.blade.php ENDPATH**/ ?>