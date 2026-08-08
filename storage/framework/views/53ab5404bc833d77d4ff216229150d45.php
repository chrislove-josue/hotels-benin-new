

<?php $__env->startSection('title', 'Chambres — ' . $establishment->name); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <a href="<?php echo e(route('admin.establishments')); ?>" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange hover:underline"><i class="bi bi-arrow-left"></i> Retour aux établissements</a>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Chambres de <?php echo e($establishment->name); ?></h1>
        <p class="mt-1 text-sm text-gray-500"><?php echo e($establishment->rooms->count()); ?> chambre(s) / espace(s).</p>
    </div>
</div>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[680px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Nom</th>
                <th class="px-5 py-3">Description</th>
                <th class="px-5 py-3">Capacité</th>
<th class="px-5 py-3">Prix / nuit</th>
                <th class="px-5 py-3">Quantité</th>
                <th class="px-5 py-3">Disponible</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $establishment->rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3 font-semibold text-hb-blue"><?php echo e($room->name); ?></td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($room->description); ?></td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($room->capacity); ?> pers.</td>
                    <td class="px-5 py-3 font-semibold text-hb-blue"><?php echo e(number_format($room->price_per_night, 0, ',', ' ')); ?> FCFA</td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($room->quantity); ?></td>
<td class="px-5 py-3">
                        <span class="badge-pill <?php echo e($room->is_available ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'); ?>">
                            <?php echo e($room->is_available ? 'Oui' : 'Non'); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="flex justify-end gap-2">
                            <a href="<?php echo e(route('admin.rooms.edit', [$establishment->id, $room->id])); ?>" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-pencil-square"></i> Modifier</a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucune chambre pour cet établissement.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views\backoffice\admin\rooms.blade.php ENDPATH**/ ?>