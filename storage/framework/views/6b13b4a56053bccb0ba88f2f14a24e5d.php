

<?php $__env->startSection('title', 'Réservations — Espace hôte'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Mes réservations</h1>
    <p class="mt-1 text-sm text-gray-500">Liste des réservations sur vos établissements.</p>
</div>

<?php echo $__env->make('backoffice.partials.filters', [
    'action' => route('hote.reservations'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Référence, voyageur…'],
        ['name' => 'statut', 'label' => 'Statut', 'type' => 'select',
         'options' => ['' => 'Tous les statuts', 'en_attente' => 'En attente', 'confirmee' => 'Confirmée', 'annulee' => 'Annulée', 'terminee' => 'Terminée']],
    ],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Établissement</th>
                <th class="px-5 py-3">Voyageur</th>
                <th class="px-5 py-3">Chambre</th>
                <th class="px-5 py-3">Dates</th>
                <th class="px-5 py-3">Montant</th>
                <th class="px-5 py-3">Statut</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $reservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3 font-semibold text-hb-blue"><?php echo e($reservation->establishment->name); ?></td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($reservation->user->name); ?></td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($reservation->room->name ?? '—'); ?></td>
                    <td class="px-5 py-3 text-gray-600">
                        <?php echo e(optional($reservation->check_in)->format('d/m/Y')); ?> → <?php echo e(optional($reservation->check_out)->format('d/m/Y')); ?>

                    </td>
                    <td class="px-5 py-3 font-semibold text-hb-blue"><?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA</td>
                    <td class="px-5 py-3">
                        <span class="badge-pill <?php echo e(match($reservation->status) { 'confirmee' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annulee' => 'bg-red-100 text-red-700', 'terminee' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' }); ?>">
                            <?php echo e($reservation->status); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <?php if(auth()->user()->isAdmin()): ?>
                            <form action="<?php echo e(route('hote.reservations.update', $reservation->id)); ?>" method="POST" class="flex justify-end gap-2">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PATCH'); ?>
                                <select name="status" class="input-field !w-36 !py-1.5 text-xs">
                                    <?php $__currentLoopData = ['en_attente', 'confirmee', 'annulee', 'terminee']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($status); ?>" <?php if($reservation->status === $status): echo 'selected'; endif; ?>><?php echo e($status); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
<button type="submit" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-arrow-repeat"></i> MAJ</button>
                            </form>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-gray-400" title="La modification du statut est réservée à l'administration de la plateforme.">
                                <i class="bi bi-lock"></i> Réservé à l'admin
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucune réservation.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <?php echo e($reservations->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/backoffice/hote/reservations.blade.php ENDPATH**/ ?>