

<?php $__env->startSection('title', 'Réservation '.$reservation->reference.' — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
    <nav class="mb-6 flex items-center gap-1 text-sm text-gray-500">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="<?php echo e(route('reservations.index')); ?>" class="hover:text-hb-blue">Mes réservations</a> <span>/</span>
        <span class="font-semibold text-hb-blue"><?php echo e($reservation->reference); ?></span>
    </nav>

    <div class="card overflow-hidden">
        
        <div class="flex flex-col gap-4 border-b border-gray-100 bg-hb-cream p-6 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="font-mono text-xs font-semibold text-hb-orange"><?php echo e($reservation->reference); ?></p>
                <h1 class="mt-1 font-display text-2xl font-bold text-hb-blue"><?php echo e($reservation->establishment->name); ?></h1>
                <p class="text-sm text-gray-500"><?php echo e($reservation->establishment->locality->name); ?></p>
            </div>
            <span class="badge-pill !px-4 !py-1.5 text-sm <?php echo e(match($reservation->status) { 'confirmee' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annulee' => 'bg-red-100 text-red-700', 'terminee' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' }); ?>">
                <?php echo e($reservation->status); ?>

            </span>
        </div>

        <div class="grid grid-cols-1 gap-6 p-6 md:grid-cols-2">
            
            <div>
                <h2 class="mb-3 font-display text-lg font-bold text-hb-blue">Détails du séjour</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Établissement</dt>
                        <dd class="font-semibold text-hb-blue"><?php echo e($reservation->establishment->name); ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Chambre</dt>
                        <dd class="font-semibold text-gray-700"><?php echo e($reservation->room->name ?? '—'); ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Arrivée</dt>
                        <dd class="font-semibold text-gray-700"><?php echo e(optional($reservation->check_in)->format('d/m/Y')); ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Départ</dt>
                        <dd class="font-semibold text-gray-700"><?php echo e(optional($reservation->check_out)->format('d/m/Y')); ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Nuits</dt>
                        <dd class="font-semibold text-gray-700"><?php echo e($reservation->nights()); ?></dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Voyageurs</dt>
                        <dd class="font-semibold text-gray-700"><?php echo e($reservation->guests); ?></dd>
                    </div>
                </dl>
            </div>

            
            <div>
                <h2 class="mb-3 font-display text-lg font-bold text-hb-blue">Paiement</h2>
                <?php if($reservation->transaction): ?>
                    <dl class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Montant</dt>
                            <dd class="font-display font-bold text-hb-blue"><?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Commission</dt>
                            <dd class="font-semibold text-gray-700"><?php echo e(number_format($reservation->commission, 0, ',', ' ')); ?> FCFA</dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Méthode</dt>
                            <dd class="font-semibold text-gray-700"><?php echo e(ucfirst(str_replace('_', ' ', $reservation->transaction->method))); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Référence</dt>
                            <dd class="font-mono text-xs text-gray-600"><?php echo e($reservation->transaction->reference); ?></dd>
                        </div>
                        <div class="flex justify-between">
                            <dt class="text-gray-500">Statut</dt>
                            <dd>
                                <span class="badge-pill <?php echo e($reservation->transaction->status === 'reussie' ? 'bg-green-100 text-green-700' : ($reservation->transaction->status === 'en_attente' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700')); ?>">
                                    <?php echo e($reservation->transaction->status); ?>

                                </span>
                            </dd>
                        </div>
                    </dl>
                <?php else: ?>
                    <p class="text-sm text-gray-500">Aucune transaction associée.</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="flex flex-wrap gap-3 border-t border-gray-100 bg-hb-cream/50 p-6">
            <?php if($reservation->status === 'en_attente'): ?>
                <a href="<?php echo e(route('reservations.checkout', $reservation)); ?>" class="btn-primary">
                    <i class="bi bi-credit-card"></i> Payer maintenant
                </a>
            <?php endif; ?>
            <a href="<?php echo e(route('establishments.show', $reservation->establishment->slug)); ?>" class="btn-outline">
                <i class="bi bi-building"></i> Voir l'établissement
            </a>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/reservations/show.blade.php ENDPATH**/ ?>