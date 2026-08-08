

<?php $__env->startSection('title', 'Utilisateurs — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Utilisateurs</h1>
        <p class="mt-1 text-sm text-gray-500">Créez, modifiez, supprimez et gérez les comptes de la plateforme.</p>
        <?php if($pendingUsers = $users->where('is_approved', false)->count()): ?>
            <p class="mt-1 text-xs font-semibold text-hb-orange"><?php echo e($pendingUsers); ?> compte(s) en attente d'approbation</p>
        <?php endif; ?>
    </div>
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn-primary !px-5 !py-2.5 text-sm">
        <i class="bi bi-person-plus"></i> Nouvel utilisateur
    </a>
</div>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[900px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Utilisateur</th>
                <th class="px-5 py-3">Téléphone / WhatsApp</th>
                <th class="px-5 py-3">Rôle</th>
                <th class="px-5 py-3">Statut</th>
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
                        <?php if($user->phone): ?>
                            <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $user->phone)); ?>" target="_blank" class="inline-flex items-center gap-1 font-mono text-xs font-semibold text-green-600 hover:underline">
                                <i class="bi bi-whatsapp"></i> <?php echo e($user->phone); ?>

                            </a>
                        <?php else: ?>
                            <span class="text-xs text-gray-400">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3">
                        <span class="badge-pill <?php echo e($user->role === 'admin' ? 'bg-hb-gold/20 text-hb-blue' : ($user->role === 'hote' ? 'bg-hb-orange/10 text-hb-orange-dark' : 'bg-gray-100 text-gray-600')); ?>">
                            <?php echo e($user->role); ?>

                        </span>
                    </td>
<td class="px-5 py-3">
                        <?php if($user->isAdmin()): ?>
                            <span class="badge-pill bg-hb-blue/10 text-hb-blue">Admin</span>
                        <?php elseif($user->is_approved): ?>
                            <span class="badge-pill bg-green-100 text-green-700"><i class="bi bi-check-circle"></i> Approuvé</span>
                        <?php else: ?>
                            <span class="badge-pill bg-yellow-100 text-yellow-700"><i class="bi bi-hourglass-split"></i> En attente</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 text-center text-gray-600"><?php echo e($user->establishments_count); ?></td>
                    <td class="px-5 py-3 text-center text-gray-600"><?php echo e($user->reservations_count); ?></td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                    <td class="px-5 py-3">
                        <div class="flex flex-wrap items-center justify-end gap-1.5">
<?php if (! ($user->isAdmin())): ?>
                                <?php if(! $user->is_approved): ?>
                                    <form action="<?php echo e(route('admin.users.approve', $user->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-outline !px-2.5 !py-1.5 text-xs" title="Approuver">
                                            <i class="bi bi-check-lg"></i> Approuver
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <form action="<?php echo e(route('admin.users.reject', $user->id)); ?>" method="POST">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn-outline !px-2.5 !py-1.5 text-xs" title="Désactiver">
                                            <i class="bi bi-x-lg"></i> Désactiver
                                        </button>
                                    </form>
                                <?php endif; ?>
                            <?php endif; ?>

                            <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn-outline !px-2.5 !py-1.5 text-xs" title="Modifier">
                                <i class="bi bi-pencil"></i> Modifier
                            </a>

                            <?php if($user->id !== auth()->id()): ?>
                                <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST"
                                      onsubmit="return confirm('Supprimer définitivement cet utilisateur ?');">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn !px-2.5 !py-1.5 text-xs bg-red-50 text-red-600 hover:bg-red-100" title="Supprimer">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-gray-400">Aucun utilisateur.</td>
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