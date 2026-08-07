

<?php $__env->startSection('title', 'Utilisateurs — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Utilisateurs</h1>
    <p class="mt-1 text-sm text-gray-500">Gérez les rôles et comptes de la plateforme.</p>
</div>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[680px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Utilisateur</th>
                <th class="px-5 py-3">Rôle</th>
                <th class="px-5 py-3">Établissements</th>
                <th class="px-5 py-3">Réservations</th>
                <th class="px-5 py-3">Inscrit le</th>
                <th class="px-5 py-3 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-full bg-hb-blue text-sm font-bold text-white"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></div>
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-hb-blue"><?php echo e($user->name); ?></p>
                                <p class="truncate text-xs text-gray-400"><?php echo e($user->email); ?></p>
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        <span class="badge-pill <?php echo e($user->role === 'admin' ? 'bg-hb-gold/20 text-hb-blue' : ($user->role === 'hote' ? 'bg-hb-orange/10 text-hb-orange-dark' : 'bg-gray-100 text-gray-600')); ?>">
                            <?php echo e($user->role); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3 text-center text-gray-600"><?php echo e($user->establishments_count); ?></td>
                    <td class="px-5 py-3 text-center text-gray-600"><?php echo e($user->reservations_count); ?></td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                    <td class="px-5 py-3">
                        <form action="<?php echo e(route('admin.users.update', $user->id)); ?>" method="POST" class="flex justify-end gap-2">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <select name="role" class="input-field !w-32 !py-1.5 text-xs">
                                <?php $__currentLoopData = ['voyageur', 'hote', 'admin']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role); ?>" <?php if($user->role === $role): echo 'selected'; endif; ?>><?php echo e($role); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
<button type="submit" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-check-lg"></i> OK</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucun utilisateur.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <?php echo e($users->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/admin/users.blade.php ENDPATH**/ ?>