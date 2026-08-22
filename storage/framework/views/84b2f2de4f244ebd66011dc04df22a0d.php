

<?php $__env->startSection('title', 'Réservations — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Réservations</h1>
    <p class="mt-1 text-sm text-gray-500">Suivi de toutes les réservations de la plateforme.</p>
</div>

<?php
    $resaLabels = ['en_attente' => 'En attente', 'confirmee' => 'Confirmée', 'annulee' => 'Annulée', 'terminee' => 'Terminée'];
?>

<?php echo $__env->make('backoffice.partials.filters', [
    'action' => route('admin.reservations'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Référence, voyageur, établissement…'],
        ['name' => 'statut', 'label' => 'Statut', 'type' => 'select', 'options' => ['' => 'Tous les statuts'] + $resaLabels],
        ['name' => 'etablissement', 'label' => 'Établissement', 'type' => 'select',
         'options' => ['' => 'Tous les établissements'] + $establishments->pluck('name', 'id')->all()],
    ],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[760px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Établissement</th>
                <th class="px-5 py-3">Voyageur</th>
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
                    <td class="px-5 py-3 text-gray-600">
                        <?php echo e(optional($reservation->check_in)->format('d/m/Y')); ?> → <?php echo e(optional($reservation->check_out)->format('d/m/Y')); ?>

                    </td>
                    <td class="px-5 py-3 font-semibold text-hb-blue"><?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA</td>
                    <td class="px-5 py-3">
                        <span class="badge-pill <?php echo e(match($reservation->status) { 'confirmee' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annulee' => 'bg-red-100 text-red-700', 'terminee' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' }); ?>">
                            <?php echo e($resaLabels[$reservation->status] ?? $reservation->status); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <form action="<?php echo e(route('admin.reservations.update', $reservation->id)); ?>" method="POST" class="flex justify-end gap-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <select name="status" class="input-field !w-36 !py-1.5 text-xs">
                                <?php $__currentLoopData = $resaLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $statusValue => $statusLabel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($statusValue); ?>" <?php if($reservation->status === $statusValue): echo 'selected'; endif; ?>><?php echo e($statusLabel); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
<button type="submit" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-arrow-repeat"></i> MAJ</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucune réservation.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <?php echo e($reservations->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/admin/reservations.blade.php ENDPATH**/ ?>