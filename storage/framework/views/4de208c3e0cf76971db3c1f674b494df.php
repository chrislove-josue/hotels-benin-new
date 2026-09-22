

<?php $__env->startSection('title', $establishment->name . ' — Hôtels Bénin'); ?>
<?php $__env->startSection('meta_description', \Illuminate\Support\Str::limit($establishment->short_description, 155)); ?>

<?php
    $shareUrl = url()->current();
    $ogImage = \Illuminate\Support\Str::startsWith($establishment->cover_image, 'http')
        ? $establishment->cover_image
        : asset($establishment->cover_image);
    $shareText = $establishment->name . ' — ' . \Illuminate\Support\Str::limit($establishment->short_description, 120);
    $shareTextEnc = urlencode($shareText);
    $whatsappShare = 'https://wa.me/?text=' . $shareTextEnc . '%0A' . urlencode($shareUrl);
    $facebookShare = 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareUrl);
    $twitterShare = 'https://twitter.com/intent/tweet?text=' . $shareTextEnc . '&url=' . urlencode($shareUrl);
?>

<?php $__env->startSection('og_meta'); ?>
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="Hôtels Bénin">
    <meta property="og:title" content="<?php echo e($establishment->name); ?> — Hôtels Bénin">
    <meta property="og:description" content="<?php echo e(\Illuminate\Support\Str::limit($establishment->short_description, 155)); ?>">
    <meta property="og:url" content="<?php echo e($shareUrl); ?>">
    <meta property="og:image" content="<?php echo e($ogImage); ?>">
    <meta property="og:image:alt" content="<?php echo e($establishment->name); ?>">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo e($establishment->name); ?>">
    <meta name="twitter:description" content="<?php echo e(\Illuminate\Support\Str::limit($establishment->short_description, 155)); ?>">
    <meta name="twitter:image" content="<?php echo e($ogImage); ?>">
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php
    $galleryImages = collect([$establishment->cover_image])->merge($establishment->images->pluck('path'))->values();
?>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin="">
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" crossorigin=""></script>

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

            
            <div class="mt-4 flex flex-wrap items-center gap-2" x-data="{ copied: false }">
                <span class="mr-1 text-xs font-semibold uppercase tracking-wide text-gray-400"><i class="bi bi-share"></i> Partager</span>
                <a href="<?php echo e($whatsappShare); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-green-700">
                    <i class="bi bi-whatsapp"></i> WhatsApp
                </a>
                <a href="<?php echo e($facebookShare); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-lg bg-blue-600 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-blue-700">
                    <i class="bi bi-facebook"></i> Facebook
                </a>
                <a href="<?php echo e($twitterShare); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-lg bg-gray-800 px-3 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-gray-900">
                    <i class="bi bi-twitter-x"></i> X
                </a>
                <button type="button" @click="navigator.clipboard.writeText('<?php echo e($shareUrl); ?>'); copied = true; setTimeout(() => copied = false, 2000)" class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-bold text-gray-700 shadow-sm transition hover:border-hb-orange hover:text-hb-orange">
                    <i class="bi" :class="copied ? 'bi-check-lg' : 'bi-link-45deg'"></i>
                    <span x-text="copied ? 'Copié !' : 'Copier le lien'"></span>
                </button>
            </div>

            
            <?php if($establishment->latitude && $establishment->longitude): ?>
                <div class="my-6 overflow-hidden rounded-2xl border border-gray-200 shadow-sm">
                    <div class="flex items-center justify-between bg-hb-blue px-4 py-2 text-xs text-white">
                        <span class="flex items-center gap-1.5 font-semibold"><i class="bi bi-geo-alt"></i> Localisation précise</span>
                        <span class="font-mono tabular-nums tracking-wide opacity-90"><?php echo e($establishment->latitude); ?>, <?php echo e($establishment->longitude); ?></span>
                    </div>
                    <div id="map" class="h-72 w-full sm:h-96"></div>
                    <div class="flex flex-wrap items-center justify-between gap-2 bg-white px-4 py-2.5 text-xs text-gray-500">
                        <span class="flex items-center gap-1"><i class="bi bi-geo-alt text-hb-orange"></i> <?php echo e($establishment->address); ?>, <?php echo e($establishment->locality->name); ?></span>
                        <div class="flex items-center gap-2">
                            <a href="https://www.google.com/maps/dir/?api=1&destination=<?php echo e($establishment->latitude); ?>,<?php echo e($establishment->longitude); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 rounded-lg bg-hb-blue/10 px-2.5 py-1 text-xs font-semibold text-hb-blue transition hover:bg-hb-blue/20">
                                <i class="bi bi-sign-turn-right"></i> Itinéraire
                            </a>
                            <a href="https://www.google.com/maps?q=<?php echo e($establishment->latitude); ?>,<?php echo e($establishment->longitude); ?>\&z=16" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 rounded-lg bg-hb-blue/10 px-2.5 py-1 text-xs font-semibold text-hb-blue transition hover:bg-hb-blue/20">
                                <i class="bi bi-arrows-expand"></i> Plein écran
                            </a>
                            <?php
    $mapShareText = 'Voici ' . $establishment->name . ' (' . $establishment->locality->name . ').';
    $mapShare = 'https://wa.me/?text=' . urlencode($mapShareText) . '%0A' . urlencode($shareUrl) . '%0ALocalisation%3A+https://maps.google.com/?q=' . $establishment->latitude . ',' . $establishment->longitude;
?>
                            <a href="<?php echo e($mapShare); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 rounded-lg bg-green-50 px-2.5 py-1 text-xs font-semibold text-green-700 transition hover:bg-green-100">
                                <i class="bi bi-share"></i> Partager
                            </a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            
            <?php if($establishment->transfer_available): ?>
                <div class="mb-6 rounded-xl border border-green-200 bg-green-50 p-5">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-green-100 text-green-700"><i class="bi bi-car-front-fill text-lg"></i></span>
                        <div class="flex-1">
                            <h3 class="font-display text-sm font-bold text-green-800">Service d'accueil / transfert aéroport</h3>
                            <?php if($establishment->transfer_description): ?>
                                <p class="mt-1 text-sm text-green-700"><?php echo e($establishment->transfer_description); ?></p>
                            <?php endif; ?>
                            <div class="mt-2 flex flex-wrap items-center gap-3">
                                <?php if($establishment->transfer_fee): ?>
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800">
                                        <i class="bi bi-tag"></i> <?php echo e(number_format($establishment->transfer_fee, 0, ',', ' ')); ?> FCFA
                                    </span>
                                <?php endif; ?>
                                <a href="https://wa.me/2290128592664?text=Bonjour,+je+souhaite+un+transfert+pour+l'établissement+<?php echo e(urlencode($establishment->name)); ?>" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3.5 py-1.5 text-xs font-bold text-white shadow-sm transition hover:bg-green-700">
                                    <i class="bi bi-whatsapp"></i> Contacter via WhatsApp
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

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
            <div class="space-y-4">
                <?php $__currentLoopData = $establishment->rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php $roomAvg = round($room->reviews->avg('rating') ?? 0, 1); ?>
                    <div class="overflow-hidden rounded-xl border border-gray-100 bg-white shadow-sm transition hover:border-hb-gold/50 hover:shadow-md">
                        
                        <div class="flex flex-col justify-between gap-2 p-4 sm:flex-row sm:items-center">
                            <div class="flex items-start gap-3">
                                <span class="mt-1 flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-hb-blue/10 text-hb-blue"><i class="bi bi-door-open"></i></span>
                                <div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="font-semibold text-hb-blue"><?php echo e($room->name); ?></p>
                                        <?php if($roomAvg > 0): ?>
                                            <span class="flex items-center gap-1 rounded-full bg-hb-gold/10 px-2 py-0.5 text-xs font-semibold text-hb-gold">
                                                ★ <?php echo e(number_format($roomAvg, 1)); ?> <span class="font-normal text-gray-400">(<?php echo e($room->reviews->count()); ?>)</span>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm text-gray-500"><?php echo e($room->description); ?></p>
                                    <p class="mt-1 text-xs text-gray-400">Capacité : <?php echo e($room->capacity); ?> pers.</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-display text-lg font-bold text-hb-blue"><?php echo e(number_format($room->price_per_night, 0, ',', ' ')); ?> FCFA</p>
                                <p class="text-xs text-gray-400">/ nuit</p>
                            </div>
                        </div>

                        
                        <?php if($room->reviews->isNotEmpty()): ?>
                            <div class="border-t border-gray-100 bg-hb-cream/50 px-4 py-3">
                                <p class="mb-2 text-xs font-bold uppercase tracking-wide text-gray-500"><i class="bi bi-chat-square-text"></i> Avis sur cette chambre (<?php echo e($room->reviews->count()); ?>)</p>
                                <div class="space-y-2">
                                    <?php $__currentLoopData = $room->reviews->take(3); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="rounded-lg bg-white p-3 shadow-sm">
                                            <div class="mb-1 flex items-center justify-between gap-2">
                                                <p class="text-sm font-semibold text-hb-blue"><?php echo e($review->user->name); ?></p>
                                                <span class="text-sm font-semibold text-hb-gold"><?php echo e(str_repeat('★', $review->rating)); ?><?php echo e(str_repeat('☆', 5 - $review->rating)); ?></span>
                                            </div>
                                            <?php if($review->title): ?>
                                                <p class="text-sm font-semibold text-gray-700"><?php echo e($review->title); ?></p>
                                            <?php endif; ?>
                                            <p class="mt-0.5 text-sm text-gray-600"><?php echo e($review->comment); ?></p>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        
                        <?php if(auth()->guard()->check()): ?>
                            <div class="border-t border-gray-100 px-4 py-3">
                                <form action="<?php echo e(route('reviews.store', $room)); ?>" method="POST" class="space-y-3" x-data="{ rating: 5 }">
                                    <?php echo csrf_field(); ?>
                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="text-sm font-semibold text-hb-blue"><i class="bi bi-pencil-square"></i> Donner mon avis sur cette chambre</p>
                                        <div class="flex gap-0.5 text-xl text-hb-gold">
                                            <template x-for="i in 5" :key="i">
                                                <button type="button" @click="rating = i" :class="i <= rating ? 'text-hb-gold' : 'text-gray-300'" class="transition hover:scale-110" aria-label="Étoile">
                                                    <i class="bi bi-star-fill"></i>
                                                </button>
                                            </template>
                                        </div>
                                    </div>
                                    <input type="hidden" name="rating" :value="rating">
                                    <div>
                                        <input type="text" name="title" placeholder="Titre (optionnel)" class="input-field !py-2 text-sm">
                                    </div>
                                    <div>
                                        <textarea name="comment" rows="2" placeholder="Racontez votre expérience dans cette chambre..." class="input-field !py-2 text-sm" required></textarea>
                                    </div>
                                    <div class="flex justify-end">
                                        <button type="submit" class="btn-primary !px-4 !py-2 text-xs"><i class="bi bi-send"></i> Publier l'avis</button>
                                    </div>
                                </form>
                            </div>
                        <?php else: ?>
                            <div class="border-t border-gray-100 bg-hb-sand/50 px-4 py-2.5 text-center text-xs text-gray-500">
                                <a href="<?php echo e(route('login.show')); ?>" class="font-semibold text-hb-orange hover:underline">Connectez-vous</a> pour laisser un avis sur cette chambre.
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>

            <div class="my-8 h-px bg-gradient-to-r from-transparent via-hb-gold/40 to-transparent"></div>

            <h2 class="mb-4 font-display text-xl font-bold text-hb-blue-deeper">Avis des voyageurs (<?php echo e($establishment->reviews_count); ?>)</h2>

            
            <?php if(auth()->guard()->check()): ?>
                <div class="mb-4 rounded-xl border border-hb-gold/30 bg-white p-4 shadow-sm">
                    <form action="<?php echo e(route('reviews.establishments.store', $establishment)); ?>" method="POST" class="space-y-3" x-data="{ rating: 5 }">
                        <?php echo csrf_field(); ?>
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <p class="text-sm font-semibold text-hb-blue"><i class="bi bi-pencil-square"></i> Donner mon avis sur <?php echo e($establishment->name); ?></p>
                            <div class="flex gap-0.5 text-xl text-hb-gold">
                                <template x-for="i in 5" :key="i">
                                    <button type="button" @click="rating = i" :class="i <= rating ? 'text-hb-gold' : 'text-gray-300'" class="transition hover:scale-110" aria-label="Étoile">
                                        <i class="bi bi-star-fill"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="rating" :value="rating">
                        <div>
                            <input type="text" name="title" placeholder="Titre (optionnel)" class="input-field !py-2 text-sm">
                        </div>
                        <div>
                            <textarea name="comment" rows="2" placeholder="Racontez votre expérience dans cet établissement..." class="input-field !py-2 text-sm" required></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="btn-primary !px-4 !py-2 text-xs"><i class="bi bi-send"></i> Publier l'avis</button>
                        </div>
                    </form>
                </div>
            <?php else: ?>
                <div class="mb-4 rounded-xl bg-hb-sand/50 px-4 py-3 text-center text-xs text-gray-500">
                    <a href="<?php echo e(route('login.show')); ?>" class="font-semibold text-hb-orange hover:underline">Connectez-vous</a> pour laisser un avis sur cet établissement.
                </div>
            <?php endif; ?>

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
                      x-data="{
                          selectedRoom: '',
                          guests: 2,
                          get maxGuests() {
                              const opt = this.$refs.roomSelect?.selectedOptions?.[0];
                              const cap = opt ? parseInt(opt.dataset.capacity) : NaN;
                              return Number.isFinite(cap) && cap > 0 ? cap : null;
                          },
                          get guestsValid() {
                              return this.maxGuests !== null && Number.isInteger(this.guests)
                                  && this.guests >= 1 && this.guests <= this.maxGuests;
                          },
                          capGuests() {
                              if (this.maxGuests === null) return;
                              if (!Number.isInteger(this.guests) || this.guests < 1) { this.guests = 1; }
                              else if (this.guests > this.maxGuests) { this.guests = this.maxGuests; }
                          },
                          updateNights() { return; }
                      }">
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
                        <select name="room_id" x-model="selectedRoom" x-ref="roomSelect" x-on:change="capGuests()" class="input-field" required>
                            <option value="">Choisir une chambre</option>
                            <?php $__currentLoopData = $establishment->rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($room->id); ?>" data-price="<?php echo e($room->price_per_night); ?>" data-capacity="<?php echo e($room->capacity); ?>">
                                    <?php echo e($room->name); ?> — <?php echo e(number_format($room->price_per_night, 0, ',', ' ')); ?> FCFA/nuit (<?php echo e($room->capacity); ?> pers. max)
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
                        <input type="number" name="guests" min="1" :max="maxGuests ?? 2000" x-model.number="guests"
                               x-on:input="capGuests()" class="input-field" required>
                        <p class="mt-1 text-xs" x-cloak
                           :class="!guestsValid && maxGuests !== null ? 'font-semibold text-red-600' : 'text-gray-400'">
                            <span x-show="maxGuests === null">La capacité maximale s'affichera après le choix de la chambre ou de l'espace.</span>
                            <span x-show="maxGuests !== null">
                                Capacité définie par l'hôte (lecture seule)&nbsp;:
                                <strong class="text-hb-blue" x-text="maxGuests"></strong> personne<span x-show="maxGuests > 1">s</span> maximum.
                                <span x-show="!guestsValid">— Le nombre saisi dépasse cette capacité et a été ajusté automatiquement.</span>
                            </span>
                        </p>
                    </div>

                    <div>
                        <button type="submit" class="btn-primary w-full" :disabled="!selectedRoom || !guestsValid">
    <i class="bi bi-calendar-check"></i> Réserver et payer
</button>

                    </div>
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

<?php if($establishment->latitude && $establishment->longitude): ?>
<style>
    .hb-map-marker-wrap { position: relative; width: 42px; height: 42px; }
    .hb-map-marker-wrap .hb-map-marker-pulse {
        position: absolute; inset: 0; border-radius: 9999px; background: rgba(245, 154, 35, 0.35);
        animation: hbMapMarkerPulse 1.8s ease-out infinite; z-index: 0;
    }
    .hb-map-marker-wrap .hb-map-marker-pin { position: absolute; left: 1px; top: 1px; width: 40px; height: 40px; display: flex; align-items: flex-end; justify-content: center; }
    .hb-map-marker-wrap .hb-map-marker-pin::before {
        content: ''; position: absolute; bottom: 2px; left: 50%; width: 26px; height: 26px;
        transform: translateX(-50%) rotate(45deg); border-radius: 50% 50% 50% 0;
        background: linear-gradient(135deg, #F59A23, #D9820F); border: 3px solid #fff;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.35);
    }
    .hb-map-marker-wrap .hb-map-marker-pin i { position: absolute; bottom: 9px; left: 50%; transform: translateX(-50%); color: #fff; font-size: 15px; z-index: 1; text-shadow: 0 1px 2px rgba(0,0,0,0.3); }
    @keyframes hbMapMarkerPulse { 0% { transform: scale(0.6); opacity: 1; } 100% { transform: scale(1.6); opacity: 0; } }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof L === 'undefined') return;
    var lat = <?php echo e($establishment->latitude); ?>;
    var lng = <?php echo e($establishment->longitude); ?>;
    var map = L.map('map', { scrollWheelZoom: false, zoomControl: true }).setView([lat, lng], 17);
    var base = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Street_Map/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri and the GIS User Community',
        maxZoom: 19, maxNativeZoom: 18
    }).addTo(map);
    L.control.scale({ imperial: false }).addTo(map);
    // Repli OSM standard si les tuiles Esri échouent.
    var fallenBack = false;
    map.on('tileerror', function () {
        if (fallenBack) return;
        fallenBack = true;
        map.removeLayer(base);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap', maxZoom: 19, maxNativeZoom: 19
        }).addTo(map);
    });
    var icon = L.divIcon({
        className: '',
        html: '<div class="hb-map-marker-wrap"><span class="hb-map-marker-pulse"></span><span class="hb-map-marker-pin"><i class="bi bi-geo-alt-fill"></i></span></div>',
        iconSize: [42, 42], iconAnchor: [21, 40], popupAnchor: [0, -36]
    });
    L.marker([lat, lng], { icon: icon }).addTo(map)
        .bindPopup('<strong style="font-size:14px;"><?php echo e(addslashes($establishment->name)); ?></strong><br><span style="color:#4b5563;"><?php echo e(addslashes($establishment->address)); ?>, <?php echo e(addslashes($establishment->locality->name)); ?></span><br><span style="font-family:monospace;font-size:12px;color:#6b7280;"><?php echo e($establishment->latitude); ?>, <?php echo e($establishment->longitude); ?></span>');

    setTimeout(function () { map.invalidateSize(); }, 200);
});
</script>
<?php endif; ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/establishments/show.blade.php ENDPATH**/ ?>