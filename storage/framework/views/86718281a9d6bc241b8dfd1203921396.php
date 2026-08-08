

<?php $__env->startSection('title', 'Établissements — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Établissements</h1>
        <p class="mt-1 text-sm text-gray-500">Gérez la vitrine et la disponibilité des établissements.</p>
    </div>
</div>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[720px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Établissement</th>
                <th class="px-5 py-3">Type</th>
                <th class="px-5 py-3">Localité</th>
                <th class="px-5 py-3">Prix / nuit</th>
                <th class="px-5 py-3">Propriétaire</th>
                <th class="px-5 py-3">Disponible</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $establishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $establishment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <img src="<?php echo e($establishment->cover_image); ?>" alt="" class="h-10 w-14 rounded-lg object-cover">
<div class="min-w-0">
                                <p class="truncate font-semibold text-hb-blue"><?php echo e($establishment->name); ?></p>
<p class="text-xs text-gray-400"><?php echo $establishment->is_featured ? '<i class="bi bi-star-fill text-hb-gold"></i> À la une' : ''; ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($establishment->type->name); ?></td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($establishment->locality->name); ?></td>
                    <td class="px-5 py-3 font-semibold text-hb-blue"><?php echo e(number_format($establishment->price_from, 0, ',', ' ')); ?> FCFA</td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($establishment->owner->name ?? '—'); ?></td>
                    <td class="px-5 py-3">
                        <span class="badge-pill <?php echo e($establishment->is_available ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'); ?>">
                            <?php echo e($establishment->is_available ? 'Active' : 'En attente'); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3">
<div class="flex justify-end gap-2">
                            <a href="<?php echo e(route('admin.establishments.edit', $establishment->id)); ?>" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-pencil-square"></i> Modifier</a>
                            <a href="<?php echo e(route('admin.establishments.rooms', $establishment->id)); ?>" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-door-open"></i> Chambres</a>
                            <form action="<?php echo e(route('admin.establishments.toggle', $establishment->id)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="btn-outline !px-3 !py-1.5 text-xs">
                                    <i class="bi <?php echo e($establishment->is_available ? 'bi-eye-slash' : 'bi-eye'); ?>"></i>
                                    <?php echo e($establishment->is_available ? 'Désactiver' : 'Activer'); ?>

                                </button>
                            </form>
                            <form action="<?php echo e(route('admin.establishments.destroy', $establishment->id)); ?>" method="POST" onsubmit="return confirm('Supprimer cet établissement ?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucun établissement.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <?php echo e($establishments->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views\backoffice\admin\establishments.blade.php ENDPATH**/ ?>