

<?php $__env->startSection('title', 'Paiement — '.$reservation->reference.' — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <nav class="mb-6 flex items-center gap-1 text-sm text-gray-500">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="hover:text-hb-blue"><?php echo e($reservation->reference); ?></a> <span>/</span>
        <span class="font-semibold text-hb-blue">Paiement</span>
    </nav>

    <div class="mb-6">
        <h1 class="font-display text-3xl font-bold text-hb-blue">Paiement de votre réservation</h1>
        <p class="mt-1 text-sm text-gray-500">Réservation <span class="font-mono font-semibold text-hb-orange"><?php echo e($reservation->reference); ?></span> · <?php echo e($reservation->establishment->name); ?></p>
    </div>

    <div class="grid grid-cols-1 gap-6">
        
        <div class="card p-6">
            <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Récapitulatif</h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-gray-500">Chambre</dt>
                    <dd class="font-semibold text-gray-700"><?php echo e($reservation->room->name ?? '—'); ?></dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Séjour</dt>
                    <dd class="font-semibold text-gray-700"><?php echo e(optional($reservation->check_in)->format('d/m/Y')); ?> → <?php echo e(optional($reservation->check_out)->format('d/m/Y')); ?> (<?php echo e($reservation->nights()); ?> <?php echo e($reservation->nights() > 1 ? 'nuits' : 'nuit'); ?>)</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Prix unitaire</dt>
                    <dd class="font-semibold text-gray-700"><?php echo e(number_format($reservation->room->price_per_night, 0, ',', ' ')); ?> FCFA / nuit</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-gray-500">Voyageurs</dt>
                    <dd class="font-semibold text-gray-700"><?php echo e($reservation->guests); ?></dd>
                </div>
                <div class="mt-3 flex justify-between border-t border-gray-100 pt-3">
                    <dt class="font-semibold text-hb-blue">Total à payer <span class="text-xs font-normal text-gray-500">(<?php echo e(number_format($reservation->room->price_per_night, 0, ',', ' ')); ?> × <?php echo e($reservation->nights()); ?> nuit(s))</span></dt>
                    <dd class="font-display text-xl font-bold text-hb-blue"><?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA</dd>
                </div>
            </dl>
        </div>

        <?php if($errors->any()): ?>
            <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <p class="flex items-center gap-2"><i class="bi bi-exclamation-triangle"></i> <?php echo e($error); ?></p>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        <?php endif; ?>

        
        <div class="card p-6">
            <?php
                $providerLabel = ($paymentProvider ?? 'fedapay') === 'kkiapay' ? 'KKiaPay (mobile money / carte)' : 'FedaPay';
            ?>
            <p class="mb-4 flex items-center gap-2 text-sm text-gray-500">
                <i class="bi bi-shield-check text-hb-blue"></i>
                Paiement sécurisé via <span class="font-semibold text-hb-blue"><?php echo e($providerLabel); ?></span>.
            </p>

            <form action="<?php echo e(route('reservations.process', $reservation)); ?>" method="POST" class="space-y-4">
                <?php echo csrf_field(); ?>


                <button type="submit" class="btn-primary w-full">
                    <i class="bi bi-check-circle"></i> Continuer vers le paiement — <?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA
                </button>
            </form>
        </div>

        <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="text-center text-sm font-semibold text-hb-blue hover:underline">
            <i class="bi bi-arrow-left"></i> Retour à la réservation
        </a>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/reservations/checkout.blade.php ENDPATH**/ ?>