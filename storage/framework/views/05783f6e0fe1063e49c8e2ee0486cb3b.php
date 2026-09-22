

<?php $__env->startSection('title', 'Mon tableau de bord — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section>
    
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-display text-3xl font-bold text-hb-blue">Bonjour, <?php echo e(auth()->user()->name); ?> <i class="bi bi-emoji-smile text-hb-gold"></i></h1>
            <p class="mt-1 text-sm text-gray-500">Bienvenue dans votre espace voyageur. Suivez vos réservations et vos paiements.</p>
        </div>
        <a href="<?php echo e(route('establishments.index')); ?>" class="btn-gold"><i class="bi bi-search"></i> Trouver un établissement</a>
    </div>

    
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="stat-card">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-blue/10 text-xl text-hb-blue"><i class="bi bi-calendar-check"></i></div>
            <div>
                <p class="text-2xl font-bold text-hb-blue"><?php echo e($totalReservations); ?></p>
                <p class="text-xs text-gray-500">Réservations</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-orange/10 text-xl text-hb-orange"><i class="bi bi-calendar-event"></i></div>
            <div>
                <p class="text-2xl font-bold text-hb-blue"><?php echo e($upcomingReservations); ?></p>
                <p class="text-xs text-gray-500">À venir</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-xl text-green-600"><i class="bi bi-credit-card"></i></div>
            <div>
                <p class="text-2xl font-bold text-hb-blue"><?php echo e(number_format($totalSpent, 0, ',', ' ')); ?> <span class="text-xs font-medium">FCFA</span></p>
                <p class="text-xs text-gray-500">Total dépensé</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-yellow-100 text-xl text-yellow-600"><i class="bi bi-hourglass-split"></i></div>
            <div>
                <p class="text-2xl font-bold text-hb-blue"><?php echo e($pendingPayments); ?></p>
                <p class="text-xs text-gray-500">Paiements en attente</p>
            </div>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-8 lg:grid-cols-3">
        
        <div class="lg:col-span-2">
            <div class="card">
                <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
                    <h2 class="font-display text-lg font-bold text-hb-blue">Prochains séjours</h2>
                    <a href="<?php echo e(route('reservations.index')); ?>" class="text-xs font-semibold text-hb-orange hover:underline">Tout voir</a>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $upcoming; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center gap-4 px-6 py-4">
                            <img src="<?php echo e($reservation->establishment->cover_image); ?>" alt="<?php echo e($reservation->establishment->name); ?>"
                                 class="h-14 w-14 shrink-0 rounded-xl object-cover">
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-center gap-2">
                                    <p class="truncate font-semibold text-hb-blue"><?php echo e($reservation->establishment->name); ?></p>
                                    <span class="font-mono text-[11px] text-hb-orange"><?php echo e($reservation->reference); ?></span>
                                </div>
                                <p class="text-xs text-gray-500">
                                    <?php echo e($reservation->room->name ?? '—'); ?> · <?php echo e(optional($reservation->check_in)->format('d/m/Y')); ?> → <?php echo e(optional($reservation->check_out)->format('d/m/Y')); ?>

                                </p>
                            </div>
                            <div class="flex shrink-0 flex-col items-end gap-1">
                                <span class="badge-pill <?php echo e(match($reservation->status) { 'confirmee' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annulee' => 'bg-red-100 text-red-700', 'terminee' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' }); ?>">
                                    <?php echo e($reservation->status); ?>

                                </span>
                                <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="text-[11px] font-semibold text-hb-blue hover:underline">Détails</a>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-10 text-center">
                            <p class="text-sm text-gray-500">Aucun séjour à venir.</p>
                            <a href="<?php echo e(route('establishments.index')); ?>" class="btn-primary mt-4">Découvrir les établissements</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="lg:col-span-1">
            <div class="card">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="font-display text-lg font-bold text-hb-blue">Paiements en attente</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $reservations->where('status', 'en_attente'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-hb-blue"><?php echo e($reservation->establishment->name); ?></p>
                                <p class="font-display font-bold text-hb-blue"><?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA</p>
                            </div>
                            <p class="mt-1 text-xs text-gray-500"><?php echo e($reservation->reference); ?></p>
                            <a href="<?php echo e(route('reservations.checkout', $reservation)); ?>" class="btn-primary mt-3 w-full !py-2 text-xs">
                                <i class="bi bi-credit-card"></i> Payer maintenant
                            </a>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="px-6 py-10 text-center">
                            <i class="bi bi-check-circle text-3xl text-green-500"></i>
                            <p class="mt-2 text-sm text-gray-500">Aucun paiement en attente.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            
            <div class="card mt-6">
                <div class="border-b border-gray-100 px-6 py-4">
                    <h2 class="font-display text-lg font-bold text-hb-blue">Réservations récentes</h2>
                </div>
                <div class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $latest; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="flex items-center justify-between px-6 py-3 transition hover:bg-hb-cream">
                            <div>
                                <p class="text-sm font-semibold text-gray-700"><?php echo e($reservation->establishment->name); ?></p>
                                <p class="font-mono text-[11px] text-gray-400"><?php echo e($reservation->reference); ?></p>
                            </div>
                            <span class="badge-pill <?php echo e($reservation->status === 'confirmee' ? 'bg-green-100 text-green-700' : ($reservation->status === 'en_attente' ? 'bg-yellow-100 text-yellow-700' : 'bg-gray-100 text-gray-600')); ?>">
                                <?php echo e($reservation->status); ?>

                            </span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="px-6 py-8 text-center text-sm text-gray-500">Aucune réservation pour le moment.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.voyageur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views\reservations\dashboard.blade.php ENDPATH**/ ?>