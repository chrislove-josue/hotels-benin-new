

<?php $__env->startSection('title', 'Tableau de bord — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-8">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Tableau de bord</h1>
    <p class="mt-1 text-sm text-gray-500">Vue d'ensemble de la plateforme Hôtels Bénin.</p>
</div>


<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-blue/10 text-xl text-hb-blue"><i class="bi bi-building"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue"><?php echo e($stats['establishments']); ?></p>
            <p class="text-xs text-gray-500">Établissements</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-orange/10 text-xl text-hb-orange"><i class="bi bi-people"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue"><?php echo e($stats['users']); ?></p>
            <p class="text-xs text-gray-500">Utilisateurs</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-gold/20 text-xl text-hb-gold"><i class="bi bi-calendar-check"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue"><?php echo e($stats['reservations']); ?></p>
            <p class="text-xs text-gray-500">Réservations</p>
            <p class="text-[11px] text-hb-orange"><?php echo e($stats['pending_reservations']); ?> en attente</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-green-100 text-xl text-green-600"><i class="bi bi-credit-card"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue"><?php echo e(number_format($stats['revenue'], 0, ',', ' ')); ?> FCFA</p>
            <p class="text-xs text-gray-500">Revenus (paiements réussis)</p>
            <p class="text-[11px] text-green-600">Commission : <?php echo e(number_format($stats['commission'], 0, ',', ' ')); ?> FCFA</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-gold/10 text-xl text-hb-gold"><i class="bi bi-clipboard-check"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue"><?php echo e($stats['pending_changes']); ?></p>
            <p class="text-xs text-gray-500">Modifications en attente</p>
            <a href="<?php echo e(route('admin.pending')); ?>" class="text-[11px] font-semibold text-hb-orange hover:underline">Examiner</a>
        </div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
    
    <div class="card lg:col-span-2">
        <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
            <h2 class="font-display text-lg font-bold text-hb-blue">Dernières réservations</h2>
            <a href="<?php echo e(route('admin.reservations')); ?>" class="text-xs font-semibold text-hb-orange hover:underline">Tout voir</a>
        </div>
        <div class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $latestReservations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $reservation): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="flex items-center justify-between gap-3 px-5 py-3">
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-gray-800"><?php echo e($reservation->establishment->name); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($reservation->user->name); ?> · <?php echo e($reservation->created_at->format('d/m/Y')); ?></p>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        <span class="text-sm font-bold text-hb-blue"><?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA</span>
                        <span class="badge-pill <?php echo e(match($reservation->status) { 'confirmee' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'annulee' => 'bg-red-100 text-red-700', 'terminee' => 'bg-blue-100 text-blue-700', default => 'bg-gray-100 text-gray-600' }); ?>">
                            <?php echo e(__($reservation->status)); ?>

                        </span>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="px-5 py-6 text-center text-sm text-gray-400">Aucune réservation pour le moment.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="space-y-6">
        
        <div class="card">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="font-display text-lg font-bold text-hb-blue">Valorisation en attente</h2>
                <p class="text-xs text-gray-500"><?php echo e($stats['unread_messages']); ?> message(s) non lu(s)</p>
            </div>
            <div class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $pendingEstablishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between gap-2 px-5 py-3">
                        <p class="truncate text-sm text-gray-700"><?php echo e($item->name); ?></p>
                        <a href="<?php echo e(route('admin.establishments')); ?>" class="text-xs font-semibold text-hb-orange">Traiter</a>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-5 py-6 text-center text-sm text-gray-400">Rien en attente.</p>
                <?php endif; ?>
            </div>
        </div>

        
        <div class="card p-5">
            <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Revenus par mois</h2>
            <div class="space-y-2">
                <?php $__empty_1 = true; $__currentLoopData = $revenueByMonth; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600"><?php echo e(\Carbon\Carbon::createFromFormat('Y-m', $row->month)->translatedFormat('M Y')); ?></span>
                        <span class="font-semibold text-hb-blue"><?php echo e(number_format($row->total, 0, ',', ' ')); ?> FCFA</span>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="text-sm text-gray-400">Aucune donnée.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/admin/dashboard.blade.php ENDPATH**/ ?>