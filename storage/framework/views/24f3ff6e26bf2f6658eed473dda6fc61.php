

<?php $__env->startSection('title', 'Mes réservations — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section>
    <nav class="mb-6 flex items-center gap-1 text-sm text-gray-500">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <span class="font-semibold text-hb-blue">Mes réservations</span>
    </nav>

    <div class="mb-8 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <h1 class="font-display text-3xl font-bold text-hb-blue">Mes réservations</h1>
            <p class="mt-1 text-sm text-gray-500">Retrouvez ici toutes vos réservations et leur statut de paiement.</p>
        </div>
    </div>

    <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card mb-4 p-5 sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-start gap-4">
                    <img src="<?php echo e($reservation->establishment->cover_image); ?>" alt="<?php echo e($reservation->establishment->name); ?>"
                         class="h-16 w-16 shrink-0 rounded-xl object-cover">
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="font-mono text-xs font-semibold text-hb-orange"><?php echo e($reservation->reference); ?></span>
                            <?php if($reservation->transaction): ?>
                                <span class="badge-pill <?php echo e($reservation->transaction->status === 'reussie' ? 'bg-green-100 text-green-700' : ($reservation->transaction->status === 'en_attente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')); ?>">
                                    <?php echo e($reservation->transaction->status); ?>

                                </span>
                            <?php endif; ?>
                        </div>
                        <p class="mt-1 font-semibold text-hb-blue"><?php echo e($reservation->establishment->name); ?></p>
                        <p class="text-sm text-gray-500">
                            <?php echo e($reservation->room->name ?? '—'); ?> · <?php echo e(optional($reservation->check_in)->format('d/m/Y')); ?> → <?php echo e(optional($reservation->check_out)->format('d/m/Y')); ?>

                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:flex-col sm:items-end">
                    <p class="font-display text-lg font-bold text-hb-blue"><?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA</p>
                    <span class="badge-pill <?php echo e(match($reservation->status) { 'confirmee' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annulee' => 'bg-red-100 text-red-700', 'terminee' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' }); ?>">
                        <?php echo e($reservation->status); ?>

                    </span>
                </div>
            </div>

            <div class="mt-4 flex justify-end border-t border-gray-100 pt-4">
                <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="btn-outline !px-4 !py-2 text-xs">
                    <i class="bi bi-eye"></i> Voir le détail
                </a>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="card p-10 text-center">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-hb-cream text-hb-blue">
                <i class="bi bi-calendar-x text-3xl"></i>
            </div>
            <h2 class="font-display text-xl font-bold text-hb-blue">Aucune réservation</h2>
            <p class="mt-2 text-sm text-gray-500">Vous n'avez pas encore effectué de réservation.</p>
            <a href="<?php echo e(route('establishments.index')); ?>" class="btn-primary mt-6">Découvrir les établissements</a>
        </div>
    <?php endif; ?>

    <div class="mt-6">
        <?php echo e($reservations->links()); ?>

    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.voyageur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/reservations/index.blade.php ENDPATH**/ ?>