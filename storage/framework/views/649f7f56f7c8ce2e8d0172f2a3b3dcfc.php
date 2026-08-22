<?php $__env->startSection('title', 'Utilisateurs — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Utilisateurs</h1>
        <p class="mt-1 text-sm text-gray-500">Hôtes classés par revenus et chambres · Voyageurs par réservations.</p>
        <?php if($pendingCount > 0): ?>
            <p class="mt-1 text-xs font-semibold text-hb-orange"><?php echo e($pendingCount); ?> compte(s) en attente d'approbation</p>
        <?php endif; ?>
    </div>
    <a href="<?php echo e(route('admin.users.create')); ?>" class="btn-primary !px-5 !py-2.5 text-sm">
        <i class="bi bi-person-plus"></i> Nouvel utilisateur
    </a>
</div>

<form method="GET" action="<?php echo e(route('admin.users')); ?>" class="card mb-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-end sm:justify-between">
    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:flex-wrap">
        <div class="sm:min-w-[240px] sm:flex-1">
            <label class="label">Rechercher</label>
            <div class="relative">
                <i class="bi bi-search pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                <input type="text" name="search" value="<?php echo e(request('search')); ?>" placeholder="Nom, e-mail, téléphone…"
                    class="input-field !pl-9">
            </div>
        </div>
        <div>
            <label class="label">Statut</label>
            <select name="statut" class="input-field">
                <option value="">Tous les statuts</option>
                <option value="en_attente" <?php if(request('statut') === 'en_attente'): echo 'selected'; endif; ?>>En attente</option>
                <option value="approuve" <?php if(request('statut') === 'approuve'): echo 'selected'; endif; ?>>Approuvé</option>
            </select>
        </div>
    </div>
    <div class="flex items-center gap-2 sm:shrink-0">
        <button type="submit" class="btn-primary !px-4 !py-2.5 text-sm">
            <i class="bi bi-funnel"></i> Filtrer
        </button>
        <?php if(request()->hasAny(['search', 'statut'])): ?>
            <a href="<?php echo e(route('admin.users')); ?>" class="btn-outline !px-4 !py-2.5 text-sm">
                <i class="bi bi-x-circle"></i> Réinitialiser
            </a>
        <?php endif; ?>
    </div>
</form>

<?php
    $initialTab = request('role') === 'voyageur' ? 'voyageurs' : (request('role') === 'admin' ? 'admins' : 'hotes');
?>

<div x-data="{ tab: '<?php echo e($initialTab); ?>' }">
    
    <div class="mb-4 flex flex-wrap gap-2">
        <button type="button" x-on:click="tab = 'hotes'"
                :class="tab === 'hotes' ? 'bg-hb-blue text-white' : 'bg-white text-gray-600 hover:bg-hb-cream'"
                class="rounded-full px-4 py-2 text-sm font-semibold shadow-sm transition">
            <i class="bi bi-building"></i> Hôtes <span class="ml-1 rounded-full px-1.5 text-xs" :class="tab === 'hotes' ? 'bg-white/20' : 'bg-hb-cream'" x-text="<?php echo e(count($hosts)); ?>"></span>
        </button>
        <button type="button" x-on:click="tab = 'voyageurs'"
                :class="tab === 'voyageurs' ? 'bg-hb-blue text-white' : 'bg-white text-gray-600 hover:bg-hb-cream'"
                class="rounded-full px-4 py-2 text-sm font-semibold shadow-sm transition">
            <i class="bi bi-suitcase-lg"></i> Voyageurs <span class="ml-1 rounded-full px-1.5 text-xs" :class="tab === 'voyageurs' ? 'bg-white/20' : 'bg-hb-cream'" x-text="<?php echo e(count($voyageurs)); ?>"></span>
        </button>
        <button type="button" x-on:click="tab = 'admins'"
                :class="tab === 'admins' ? 'bg-hb-blue text-white' : 'bg-white text-gray-600 hover:bg-hb-cream'"
                class="rounded-full px-4 py-2 text-sm font-semibold shadow-sm transition">
            <i class="bi bi-shield-lock"></i> Admins <span class="ml-1 rounded-full px-1.5 text-xs" :class="tab === 'admins' ? 'bg-white/20' : 'bg-hb-cream'" x-text="<?php echo e(count($admins)); ?>"></span>
        </button>
    </div>

    
    <div x-show="tab === 'hotes'" x-cloak>
        <div class="card overflow-x-auto">
            <table class="w-full min-w-[1000px] text-left text-sm">
                <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Rang</th>
                        <th class="px-5 py-3">Hôte</th>
                        <th class="px-5 py-3">Téléphone</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3 text-center">Établissements</th>
                        <th class="px-5 py-3 text-center">Chambres</th>
                        <th class="px-5 py-3 text-center">Réservations reçues</th>
                        <th class="px-5 py-3 text-right">Revenus encaissés</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $hosts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-hb-cream/50">
                            <td class="px-5 py-3">
                                <?php if($loop->index === 0): ?>
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-hb-gold to-hb-orange text-sm text-white"><i class="bi bi-trophy"></i></span>
                                <?php elseif($loop->index < 3): ?>
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-hb-gold/15 text-sm font-bold text-hb-gold"><?php echo e($loop->iteration); ?></span>
                                <?php else: ?>
                                    <span class="pl-2 font-bold text-gray-400"><?php echo e($loop->iteration); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-hb-orange/15 text-sm font-bold text-hb-orange-dark"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></div>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-hb-blue"><?php echo e($user->name); ?></p>
                                        <p class="truncate text-xs text-gray-400"><?php echo e($user->email); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <?php if($user->phone): ?>
                                    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $user->phone)); ?>" target="_blank" class="inline-flex items-center gap-1 font-mono text-xs font-semibold text-green-600 hover:underline"><i class="bi bi-whatsapp"></i> <?php echo e($user->phone); ?></a>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <?php if($user->is_approved): ?>
                                    <span class="badge-pill bg-green-100 text-green-700"><i class="bi bi-check-circle"></i> Approuvé</span>
                                <?php else: ?>
                                    <span class="badge-pill bg-yellow-100 text-yellow-700"><i class="bi bi-hourglass-split"></i> En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-center font-semibold text-gray-700"><?php echo e($user->establishments_count); ?></td>
                            <td class="px-5 py-3 text-center font-bold text-hb-blue"><?php echo e($user->rooms_count); ?></td>
                            <td class="px-5 py-3 text-center text-gray-600"><?php echo e($user->host_reservations_count); ?></td>
                            <td class="px-5 py-3 text-right font-bold text-green-600"><?php echo e(number_format((float) $user->revenue, 0, ',', ' ')); ?> FCFA</td>
                            <td class="px-5 py-3"><?php echo $__env->make('backoffice.admin.partials.user-actions', ['user' => $user], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="9" class="px-5 py-8 text-center text-gray-400">Aucun hôte.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div x-show="tab === 'voyageurs'" x-cloak>
        <div class="card overflow-x-auto">
            <table class="w-full min-w-[900px] text-left text-sm">
                <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Rang</th>
                        <th class="px-5 py-3">Voyageur</th>
                        <th class="px-5 py-3">Téléphone</th>
                        <th class="px-5 py-3">Statut</th>
                        <th class="px-5 py-3 text-center">Réservations</th>
                        <th class="px-5 py-3 text-right">Total dépensé</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $voyageurs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-hb-cream/50">
                            <td class="px-5 py-3">
                                <?php if($loop->index === 0): ?>
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-gradient-to-br from-hb-gold to-hb-orange text-sm text-white"><i class="bi bi-trophy"></i></span>
                                <?php elseif($loop->index < 3): ?>
                                    <span class="flex h-8 w-8 items-center justify-center rounded-full bg-hb-gold/15 text-sm font-bold text-hb-gold"><?php echo e($loop->iteration); ?></span>
                                <?php else: ?>
                                    <span class="pl-2 font-bold text-gray-400"><?php echo e($loop->iteration); ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full <?php echo e($user->role === 'admin' ? 'bg-hb-gold/20 text-hb-blue' : ($user->role === 'hote' ? 'bg-hb-orange/15 text-hb-orange-dark' : 'bg-hb-blue/10 text-hb-blue')); ?> text-sm font-bold"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></div>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-hb-blue"><?php echo e($user->name); ?>

                                            <?php if($user->role === 'hote'): ?>
                                                <span class="badge-pill ml-1 bg-hb-orange/10 text-hb-orange-dark" title="Cet utilisateur est aussi un hôte"><i class="bi bi-building"></i> aussi hôte</span>
                                            <?php elseif($user->role === 'admin'): ?>
                                                <span class="badge-pill ml-1 bg-hb-gold/20 text-hb-blue" title="Cet utilisateur est aussi un administrateur"><i class="bi bi-shield-lock"></i> admin</span>
                                            <?php endif; ?>
                                        </p>
                                        <p class="truncate text-xs text-gray-400"><?php echo e($user->email); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <?php if($user->phone): ?>
                                    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $user->phone)); ?>" target="_blank" class="inline-flex items-center gap-1 font-mono text-xs font-semibold text-green-600 hover:underline"><i class="bi bi-whatsapp"></i> <?php echo e($user->phone); ?></a>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3">
                                <?php if($user->is_approved): ?>
                                    <span class="badge-pill bg-green-100 text-green-700"><i class="bi bi-check-circle"></i> Approuvé</span>
                                <?php else: ?>
                                    <span class="badge-pill bg-yellow-100 text-yellow-700"><i class="bi bi-hourglass-split"></i> En attente</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-center font-bold text-hb-blue"><?php echo e($user->voyageur_reservations_count); ?></td>
                            <td class="px-5 py-3 text-right font-semibold text-green-600"><?php echo e(number_format((float) $user->spent, 0, ',', ' ')); ?> FCFA</td>
                            <td class="px-5 py-3"><?php echo $__env->make('backoffice.admin.partials.user-actions', ['user' => $user], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucun voyageur.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div x-show="tab === 'admins'" x-cloak>
        <div class="card overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-sm">
                <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
                    <tr>
                        <th class="px-5 py-3">Administrateur</th>
                        <th class="px-5 py-3">Téléphone</th>
                        <th class="px-5 py-3">Inscrit le</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php $__empty_1 = true; $__currentLoopData = $admins; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr class="hover:bg-hb-cream/50">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-9 w-9 items-center justify-center rounded-full bg-hb-gold/20 text-sm font-bold text-hb-blue"><?php echo e(strtoupper(substr($user->name, 0, 1))); ?></div>
                                    <div class="min-w-0">
                                        <p class="truncate font-semibold text-hb-blue"><?php echo e($user->name); ?> <?php if($user->id === auth()->id()): ?><span class="badge-pill ml-1 bg-hb-blue/10 text-hb-blue">vous</span><?php endif; ?></p>
                                        <p class="truncate text-xs text-gray-400"><?php echo e($user->email); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <?php if($user->phone): ?>
                                    <?php echo e($user->phone); ?>

                                <?php else: ?>
                                    <span class="text-xs text-gray-400">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-3 text-gray-600"><?php echo e($user->created_at->format('d/m/Y')); ?></td>
                            <td class="px-5 py-3 text-right text-xs text-gray-400">
                                <?php if (! ($user->id === auth()->id())): ?>
                                    <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-pencil-square"></i> Modifier</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400">Aucun administrateur.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/admin/users.blade.php ENDPATH**/ ?>