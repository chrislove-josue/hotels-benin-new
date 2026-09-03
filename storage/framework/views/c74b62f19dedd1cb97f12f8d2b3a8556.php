

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


<div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-xl text-emerald-600"><i class="bi bi-door-open"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue"><?php echo e($stats['available_units']); ?> <span class="text-sm font-semibold text-gray-400">/ <?php echo e($stats['total_units']); ?></span></p>
            <p class="text-xs text-gray-500">Chambres libres en ce moment</p>
            <p class="text-[11px] text-gray-400"><?php echo e($stats['room_types']); ?>, type(s) de chambre</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-blue/10 text-xl text-hb-blue"><i class="bi bi-percent"></i></div>
        <div class="w-full">
            <p class="text-2xl font-bold text-hb-blue"><?php echo e($stats['occupancy_rate']); ?> %</p>
            <p class="text-xs text-gray-500">Taux d'occupation du jour</p>
            <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-gray-100">
                <div class="h-full rounded-full bg-gradient-to-r from-hb-gold to-hb-orange" style="width: <?php echo e($stats['occupancy_rate']); ?>%"></div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-hb-orange/10 text-xl text-hb-orange"><i class="bi bi-box-arrow-in-right"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue"><?php echo e($stats['arrivals_today']); ?></p>
            <p class="text-xs text-gray-500">Arrivées aujourd'hui</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-sky-100 text-xl text-sky-600"><i class="bi bi-box-arrow-right"></i></div>
        <div>
            <p class="text-2xl font-bold text-hb-blue"><?php echo e($stats['departures_today']); ?></p>
            <p class="text-xs text-gray-500">Départs aujourd'hui</p>
        </div>
    </div>
</div>


<div class="card mt-6">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <div>
            <h2 class="font-display text-lg font-bold text-hb-blue">Dernières transactions</h2>
            <p class="text-xs text-gray-500">
                <span class="font-semibold text-green-600"><?php echo e($stats['tx_reussie']['count']); ?> réussie(s)</span> ·
                <span class="font-semibold text-yellow-600"><?php echo e($stats['tx_en_attente']['count']); ?> en attente</span> ·
                <span class="font-semibold text-red-600"><?php echo e($stats['tx_echouee']['count']); ?> échouée(s)</span>
                — Encaissé : <?php echo e(number_format($stats['tx_reussie']['amount'], 0, ',', ' ')); ?> FCFA
            </p>
        </div>
        <a href="<?php echo e(route('admin.transactions')); ?>" class="text-xs font-semibold text-hb-orange hover:underline">Tout voir</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400">
                    <th class="px-5 py-3 font-semibold">Référence</th>
                    <th class="px-5 py-3 font-semibold">Réservation</th>
                    <th class="px-5 py-3 font-semibold">Voyageur</th>
                    <th class="px-5 py-3 font-semibold">Établissement</th>
                    <th class="px-5 py-3 text-right font-semibold">Montant</th>
                    <th class="px-5 py-3 text-right font-semibold">Commission</th>
                    <th class="px-5 py-3 font-semibold">Méthode</th>
                    <th class="px-5 py-3 font-semibold">Statut</th>
                    <th class="px-5 py-3 font-semibold">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php
                    $txStatusStyles = [
                        'reussie' => 'bg-green-100 text-green-700',
                        'en_attente' => 'bg-yellow-100 text-yellow-700',
                        'echouee' => 'bg-red-100 text-red-700',
                    ];
                    $txLabels = ['reussie' => 'Réussie', 'en_attente' => 'En attente', 'echouee' => 'Échouée', 'remboursee' => 'Remboursée'];
                ?>
                <?php $__empty_1 = true; $__currentLoopData = $latestTransactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-5 py-3 font-mono text-xs font-semibold text-hb-blue"><?php echo e($transaction->reference); ?></td>
                        <td class="px-5 py-3">
                            <?php if($transaction->reservation): ?>
                                <?php echo e($transaction->reservation->reference); ?>

                            <?php else: ?>
                                <span class="text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3"><?php echo e($transaction->reservation?->user?->name ?? '—'); ?></td>
                        <td class="max-w-[180px] truncate px-5 py-3"><?php echo e($transaction->reservation?->establishment?->name ?? '—'); ?></td>
                        <td class="px-5 py-3 text-right font-bold text-hb-blue"><?php echo e(number_format($transaction->amount, 0, ',', ' ')); ?> FCFA</td>
                        <td class="px-5 py-3 text-right text-gray-500"><?php echo e(number_format($transaction->commission, 0, ',', ' ')); ?></td>
                        <td class="px-5 py-3">
                            <?php if($transaction->method): ?>
                                <span class="capitalize"><?php echo e($transaction->method); ?></span>
                            <?php else: ?>
                                <span class="text-gray-400">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3">
                            <span class="badge-pill <?php echo e($txStatusStyles[$transaction->status] ?? 'bg-gray-100 text-gray-600'); ?>">
                                <?php echo e($txLabels[$transaction->status] ?? $transaction->status); ?>

                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500"><?php echo e($transaction->created_at->format('d/m/Y H:i')); ?></td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="9" class="px-5 py-6 text-center text-sm text-gray-400">Aucune transaction pour le moment.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


<div class="card mt-6">
    <div class="flex items-center justify-between border-b border-gray-100 px-5 py-4">
        <div>
            <h2 class="font-display text-lg font-bold text-hb-blue">Classement des établissements</h2>
            <p class="text-xs text-gray-500">Top 10 — par revenus encaissés et nombre de clients distincts (réservations confirmées ou terminées).</p>
        </div>
        <a href="<?php echo e(route('admin.establishments')); ?>" class="text-xs font-semibold text-hb-orange hover:underline">Tout voir</a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full min-w-[720px] text-sm">
            <thead>
                <tr class="border-b border-gray-100 text-left text-xs uppercase tracking-wide text-gray-400">
                    <th class="px-5 py-3 font-semibold">#</th>
                    <th class="px-5 py-3 font-semibold">Établissement</th>
                    <th class="px-5 py-3 font-semibold">Hôte</th>
                    <th class="px-5 py-3 text-right font-semibold">Clients</th>
                    <th class="px-5 py-3 text-right font-semibold">Réservations</th>
                    <th class="px-5 py-3 text-right font-semibold">Revenus encaissés</th>
                    <th class="px-5 py-3 text-right font-semibold">Commission</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $topEstablishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-5 py-3">
                            <?php if($i === 0): ?>
                                <span class="flex h-7 w-7 items-center justify-center rounded-full bg-gradient-to-br from-hb-gold to-hb-orange text-xs font-bold text-white"><i class="bi bi-trophy"></i></span>
                            <?php else: ?>
                                <span class="text-sm font-bold text-gray-400"><?php echo e($i + 1); ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 font-semibold text-hb-blue"><?php echo e($item->name); ?></td>
                        <td class="px-5 py-3 text-gray-600"><?php echo e($item->host_name ?? '—'); ?></td>
                        <td class="px-5 py-3 text-right">
                            <span class="font-bold text-hb-blue"><?php echo e($item->clients_count); ?></span>
                            <span class="text-xs text-gray-400">client(s)</span>
                        </td>
                        <td class="px-5 py-3 text-right text-gray-600"><?php echo e($item->reservations_count); ?></td>
                        <td class="px-5 py-3 text-right font-bold text-green-600"><?php echo e(number_format($item->revenue, 0, ',', ' ')); ?> FCFA</td>
                        <td class="px-5 py-3 text-right text-gray-500"><?php echo e(number_format($item->commission, 0, ',', ' ')); ?> FCFA</td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="px-5 py-6 text-center text-sm text-gray-400">Aucun établissement pour le moment.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
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
            <div class="flex items-center justify-between gap-3 border-b border-gray-50 px-5 py-3">
                <div>
                    <p class="text-sm font-semibold text-gray-700">Comptes en attente</p>
                    <p class="text-xs text-gray-400">Hôtes & voyageurs à valider</p>
                </div>
                <a href="<?php echo e(route('admin.users')); ?>" class="flex items-center gap-1 text-xs font-bold text-hb-orange">
                    <?php echo e($stats['pending_users']); ?> <i class="bi bi-arrow-right"></i>
                </a>
            </div>
            <div class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $pendingEstablishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="flex items-center justify-between gap-2 px-5 py-3">
                        <div class="flex min-w-0 items-center gap-3">
                            <?php if($item->cover_image): ?>
                                <img src="<?php echo e($item->cover_image); ?>" alt="<?php echo e($item->name); ?>" class="h-10 w-14 shrink-0 rounded-lg object-cover">
                            <?php else: ?>
                                <span class="flex h-10 w-14 shrink-0 items-center justify-center rounded-lg bg-gray-100 text-gray-300"><i class="bi bi-building"></i></span>
                            <?php endif; ?>
                            <p class="truncate text-sm text-gray-700"><?php echo e($item->name); ?></p>
                        </div>
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

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/backoffice/admin/dashboard.blade.php ENDPATH**/ ?>