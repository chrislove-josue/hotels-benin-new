

<?php $__env->startSection('title', $event->title . ' — Hôtels Bénin'); ?>
<?php $__env->startSection('meta_description', \Illuminate\Support\Str::limit($event->short_description, 155)); ?>

<?php $__env->startSection('content'); ?>
<section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8">
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="<?php echo e(route('events.index')); ?>" class="hover:text-hb-blue">Événements</a> <span>/</span>
        <span class="font-semibold text-hb-blue"><?php echo e($event->title); ?></span>
    </nav>

    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">
        
        <div class="lg:col-span-2">
            <div class="relative h-72 overflow-hidden rounded-2xl shadow-card md:h-96">
                <img src="<?php echo e($event->cover_image); ?>" alt="<?php echo e($event->title); ?>" class="h-full w-full object-cover">
                <div class="absolute inset-0 bg-gradient-to-t from-hb-blue-deeper/70 to-transparent"></div>
                <div class="absolute bottom-5 left-5 right-5">
                    <span class="mb-2 inline-flex items-center gap-1 rounded-full bg-hb-gold px-3 py-1 text-xs font-bold text-hb-blue-deeper shadow">
                        <i class="bi <?php echo e($event->category_icon); ?>"></i> <?php echo e($event->category_label); ?>

                    </span>
                    <h1 class="font-display text-2xl font-bold text-white md:text-3xl"><?php echo e($event->title); ?></h1>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-blue/10 text-hb-blue"><i class="bi bi-calendar-event"></i></span>
                        <div>
                            <p class="text-xs text-gray-500">Dates</p>
                            <p class="text-sm font-semibold text-hb-blue">
                                <?php echo e($event->date_start->format('d M Y')); ?>

                                <?php if($event->date_end && !$event->date_end->isSameDay($event->date_start)): ?>
                                    → <?php echo e($event->date_end->format('d M Y')); ?>

                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-orange/10 text-hb-orange"><i class="bi bi-geo-alt"></i></span>
                        <div>
                            <p class="text-xs text-gray-500">Lieu</p>
                            <p class="text-sm font-semibold text-hb-blue"><?php echo e($event->location); ?></p>
                        </div>
                    </div>
                </div>
                <div class="rounded-xl bg-white p-4 shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-gold/10 text-hb-gold"><i class="bi bi-tag"></i></span>
                        <div>
                            <p class="text-xs text-gray-500">Catégorie</p>
                            <p class="text-sm font-semibold text-hb-blue"><?php echo e($event->category_label); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="my-8 h-px bg-gradient-to-r from-transparent via-hb-gold/40 to-transparent"></div>

            <h2 class="mb-3 font-display text-xl font-bold text-hb-blue-deeper">À propos de l'événement</h2>
            <p class="leading-relaxed text-gray-600"><?php echo e($event->description); ?></p>
        </div>

        
        <div class="lg:col-span-1">
            <div class="card sticky top-24 p-6">
                <h3 class="font-display text-lg font-bold text-hb-blue">Préparez votre séjour</h3>
                <p class="mt-2 text-sm text-gray-500">
                    Cet événement attire de nombreux visiteurs. Réservez votre hébergement à <?php echo e($event->location); ?> pour profiter pleinement de l'expérience.
                </p>

                <div class="mt-4 rounded-xl bg-hb-cream p-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-500">Dates</span>
                        <span class="font-semibold text-hb-blue"><?php echo e($event->date_start->format('d/m/Y')); ?><?php echo e($event->date_end && !$event->date_end->isSameDay($event->date_start) ? ' — '.$event->date_end->format('d/m/Y') : ''); ?></span>
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-gray-500">Lieu</span>
                        <span class="font-semibold text-hb-blue"><?php echo e($event->location); ?></span>
                    </div>
                </div>

                <a href="<?php echo e(route('establishments.index', ['locality' => \Illuminate\Support\Str::slug($event->location)])); ?>"
                   class="btn-primary mt-5 w-full">
                    <i class="bi bi-search"></i> Trouver un hébergement
                </a>
                <a href="<?php echo e(route('events.index')); ?>" class="btn-outline mt-2 w-full">
                    <i class="bi bi-arrow-left"></i> Tous les événements
                </a>
            </div>
        </div>
    </div>

    
    <?php if($similar->isNotEmpty()): ?>
        <div class="mt-16" data-reveal>
            <span class="section-eyebrow">Dans la même catégorie</span>
            <h2 class="section-title mb-8">Événements similaires</h2>
            <div class="divider-gold"></div>
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php $__currentLoopData = $similar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('events.show', $item->slug)); ?>" class="card group overflow-hidden">
                        <div class="relative h-40 overflow-hidden">
                            <img src="<?php echo e($item->cover_image); ?>" alt="<?php echo e($item->title); ?>" class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy">
                            <div class="absolute inset-0 bg-gradient-to-t from-hb-blue-deeper/70 to-transparent"></div>
                            <span class="absolute left-3 top-3 rounded-full bg-white/90 px-3 py-1 text-[11px] font-bold text-hb-blue shadow backdrop-blur">
                                <i class="bi <?php echo e($item->category_icon); ?> text-hb-orange"></i> <?php echo e($item->category_label); ?>

                            </span>
                            <p class="absolute bottom-3 left-3 flex items-center gap-1 text-[11px] font-semibold text-white">
                                <i class="bi bi-calendar3 text-hb-gold"></i> <?php echo e($item->date_start->format('d M Y')); ?>

                            </p>
                        </div>
                        <div class="p-5">
                            <h3 class="font-display text-lg font-bold leading-snug text-hb-blue transition group-hover:text-hb-orange"><?php echo e($item->title); ?></h3>
                            <p class="mt-1 flex items-center gap-1 text-xs text-gray-500"><i class="bi bi-geo-alt text-hb-orange"></i> <?php echo e($item->location); ?></p>
                        </div>
                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views\events\show.blade.php ENDPATH**/ ?>