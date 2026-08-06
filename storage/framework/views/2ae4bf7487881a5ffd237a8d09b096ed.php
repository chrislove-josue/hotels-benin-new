<?php $__env->startSection('title', $establishment->name . ' — Hôtels Bénin'); ?>
<?php $__env->startSection('meta_description', \Illuminate\Support\Str::limit($establishment->short_description, 155)); ?>

<?php $__env->startSection('content'); ?>
<?php
    $galleryImages = collect([$establishment->cover_image])->merge($establishment->images->pluck('path'))->values();
?>
<div x-data="{ lightbox: null, images: <?php echo e($galleryImages->toJson()); ?> }">


<section class="mx-auto max-w-7xl px-4 pt-8 sm:px-6 lg:px-8" data-reveal>
    <nav class="mb-4 flex items-center gap-1 text-sm text-gray-500">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="<?php echo e(route('establishments.index')); ?>" class="hover:text-hb-blue">Établissements</a> <span>/</span>
        <span class="font-semibold text-hb-blue"><?php echo e($establishment->name); ?></span>
    </nav>


    <div class="grid grid-cols-1 gap-3 md:grid-cols-4 md:grid-rows-2">
        <button type="button" class="group relative overflow-hidden rounded-2xl md:col-span-2 md:row-span-2" @click="lightbox = 0">
            <img src="<?php echo e($galleryImages[0]); ?>" alt="<?php echo e($establishment->name); ?>" class="h-64 w-full object-cover transition duration-700 group-hover:scale-105 md:h-full">
            <span class="absolute inset-0 bg-hb-blue-deeper/0 transition group-hover:bg-hb-blue-deeper/20"></span>
        </button>
        <?php $__currentLoopData = $galleryImages->slice(1, 4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <button type="button" class="group relative overflow-hidden rounded-2xl" @click="lightbox = <?php echo e($loop->iteration); ?>">
                <img src="<?php echo e($image); ?>" alt="" class="h-32 w-full object-cover transition duration-700 group-hover:scale-105 md:h-full">
                <span class="absolute inset-0 bg-hb-blue-deeper/0 transition group-hover:bg-hb-blue-deeper/20"></span>
            </button>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</section>

<section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-10 lg:grid-cols-3">

        
        <div class="lg:col-span-2">
            <div class="mb-4 flex flex-wrap items-center gap-3">
                <span class="badge-gold-solid"><?php echo e($establishment->type->name); ?></span>
                <span class="flex items-center gap-1 rounded-full bg-hb-gold/10 px-3 py-1 text-sm font-semibold text-hb-gold">
                    ★ <?php echo e(number_format($establishment->rating, 1)); ?> · <?php echo e($establishment->reviews_count); ?> avis
                </span>
            </div>

            <h1 class="font-display text-3xl font-bold text-hb-blue-deeper md:text-4xl"><?php echo e($establishment->name); ?></h1>
<p class="mt-2 flex items-center gap-1 text-gray-500">
                <i class="bi bi-geo-alt text-hb-orange"></i> <?php echo e($establishment->address); ?>, <?php echo e($establishment->locality->name); ?>

            </p>

            <div class="my-8 h-px bg-gradient-to-r from-transparent via-hb-gold/40 to-transparent"></div>

            <h2 class="mb-3 font-display text-xl font-bold text-hb-blue-deeper">Description</h2>
            <p class="leading-relaxed text-gray-600"><?php echo e($establishment->description); ?></p>

            <div class="my-8 h-px bg-gradient-to-r from-transparent via-hb-gold/40 to-transparent"></div>

            <h2 class="mb-4 font-display text-xl font-bold text-hb-blue-deeper">Équipements</h2>
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                <?php $__currentLoopData = $establishment->amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-center gap-2 rounded-xl bg-white px-3 py-2 text-sm text-gray-700 shadow-sm">
<span class="flex h-5 w-5 items-center justify-center rounded-full bg-hb-gold/20 text-xs text-hb-orange"><i class="bi bi-check-lg"></i></span> <?php echo e($amenity->name); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="my-8 h-px bg-gradient-to-r from-transparent via-hb-gold/40 to-transparent"></div>

            <h2 class="mb-4 font-display text-xl font-bold text-hb-blue-deeper">Chambres / espaces disponibles</h2>
            <div class="space-y-3">
                <?php $__currentLoopData = $establishment->rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex flex-col justify-between gap-2 rounded-xl border border-gray-100 bg-white p-4 shadow-sm transition hover:border-hb-gold/50 hover:shadow-md sm:flex-row sm:items-center">
                        <div class="flex items-start gap-3">
<span class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-hb-blue/10 text-hb-blue"><i class="bi bi-door-open"></i></span>
                            <div>
                                <p class="font-semibold text-hb-blue"><?php echo e($room->name); ?></p>
                                <p class="text-sm text-gray-500"><?php echo e($room->description); ?></p>
                                <p class="mt-1 text-xs text-gray-400">Capacité : <?php echo e($room->capacity); ?> pers.</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-display text-lg font-bold text-hb-blue"><?php echo e(number_format($room->price_per_night, 0, ',', ' ')); ?> FCFA</p>
                            <p class="text-xs text-gray-400">/ nuit</p>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="my-8 h-px bg-gradient-to-r from-transparent via-hb-gold/40 to-transparent"></div>

            <h2 class="mb-4 font-display text-xl font-bold text-hb-blue-deeper">Avis des voyageurs (<?php echo e($establishment->reviews_count); ?>)</h2>
            <div class="space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $establishment->reviews->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="rounded-xl bg-white p-4 shadow-sm">
                        <div class="mb-1 flex items-center justify-between">
                            <p class="font-semibold text-hb-blue"><?php echo e($review->user->name); ?></p>
                            <span class="text-sm font-semibold text-hb-gold"><?php echo e(str_repeat('★', $review->rating)); ?><?php echo e(str_repeat('☆', 5 - $review->rating)); ?></span>
                        </div>
                        <?php if($review->title): ?>
                            <p class="text-sm font-semibold text-gray-700"><?php echo e($review->title); ?></p>
                        <?php endif; ?>
                        <p class="mt-1 text-sm text-gray-600"><?php echo e($review->comment); ?></p>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-gray-500">Aucun avis pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>


        <div class="lg:col-span-1">
            <div class="card sticky top-24 p-6">
                <p class="mb-1 text-xs text-gray-400">à partir de</p>
                <p class="mb-4 font-display text-3xl font-bold text-hb-blue">
                    <?php echo e(number_format($establishment->price_from, 0, ',', ' ')); ?> <span class="text-sm font-medium">FCFA / nuit</span>
                </p>

                <?php if(auth()->guard()->guest()): ?>
                    <div class="rounded-xl bg-hb-sand p-5 text-center">
                        <p class="text-sm text-gray-600">Connectez-vous pour réserver cet établissement.</p>
                        <a href="<?php echo e(route('login.show')); ?>" class="btn-primary mt-4 w-full">Se connecter</a>
                        <a href="<?php echo e(route('register.show')); ?>" class="btn-outline mt-2 w-full">Créer un compte</a>
                    </div>
                <?php else: ?>

<form action="<?php echo e(route('reservations.store', $establishment->slug)); ?>" method="POST" class="space-y-4"
                      x-data="{ selectedRoom: '', updateNights() { return; } }">
                    <?php echo csrf_field(); ?>

                    <?php if($errors->any()): ?>
                        <div class="rounded-xl bg-red-50 p-3 text-xs text-red-600">
                            <ul class="space-y-1">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>• <?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Chambre / espace</label>
                        <select name="room_id" x-model="selectedRoom" class="input-field" required>
                            <option value="">Choisir une chambre</option>
                            <?php $__currentLoopData = $establishment->rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($room->id); ?>" data-price="<?php echo e($room->price_per_night); ?>">
                                    <?php echo e($room->name); ?> — <?php echo e(number_format($room->price_per_night, 0, ',', ' ')); ?> FCFA/nuit
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-500">Arrivée</label>
                            <input type="date" name="check_in" class="input-field" required
                                   x-on:change="updateNights()">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-500">Départ</label>
                            <input type="date" name="check_out" class="input-field" required
                                   x-on:change="updateNights()">
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Voyageurs</label>
                        <input type="number" name="guests" min="1" max="20" value="2" class="input-field" required>
                    </div>

                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Moyen de paiement</label>
                        <select name="payment_method" class="input-field" required>
                            <option value="mtn_momo">MTN Mobile Money</option>
                            <option value="moov_money">Moov Money</option>
                            <option value="card">Carte bancaire</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-primary w-full" :disabled="!selectedRoom">
                        <i class="bi bi-calendar-check"></i> Réserver
                    </button>

                    <p class="text-center text-xs text-gray-400">
                        <i class="bi bi-shield-check"></i> Paiement sécurisé simulé
                    </p>
                </form>

                <?php endif; ?>

                <div class="mt-6 border-t border-gray-100 pt-6 text-center text-sm text-gray-500">
                    Une question ? <a href="<?php echo e(route('contact.create')); ?>" class="font-semibold text-hb-orange">Contactez-nous</a>
                </div>
            </div>
        </div>
    </div>

    
    <?php if($similar->isNotEmpty()): ?>
        <div class="mt-16" data-reveal>
            <span class="section-eyebrow">Dans la même ville</span>
            <h2 class="section-title mb-8">Établissements similaires à <?php echo e($establishment->locality->name); ?></h2>
            <div class="divider-gold"></div>
            <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <?php $__currentLoopData = $similar; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('partials.establishment-card', ['establishment' => $item], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</section>


<div x-cloak x-show="lightbox !== null" @keydown.escape.window="lightbox = null"
     class="fixed inset-0 z-[60] flex items-center justify-center bg-hb-blue-deeper/90 p-4 backdrop-blur-sm"
     x-transition.opacity @click.self="lightbox = null">
<button @click="lightbox = null" class="absolute right-5 top-5 flex h-11 w-11 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20"><i class="bi bi-x-lg text-xl"></i></button>

    <button @click="lightbox = (lightbox + images.length - 1) % images.length" class="absolute left-4 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20"><i class="bi bi-chevron-left text-2xl"></i></button>

    <img :src="images[lightbox]" class="max-h-[80vh] max-w-full rounded-2xl object-contain shadow-2xl">

    <button @click="lightbox = (lightbox + 1) % images.length" class="absolute right-4 flex h-12 w-12 items-center justify-center rounded-full bg-white/10 text-white hover:bg-white/20"><i class="bi bi-chevron-right text-2xl"></i></button>
</div>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/establishments/show.blade.php ENDPATH**/ ?>