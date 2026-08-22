

<?php $__env->startSection('title', 'Transactions — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Transactions</h1>
        <p class="mt-1 text-sm text-gray-500">Historique des paiements et commissions.</p>
    </div>
    <?php if($total): ?>
        <div class="card px-5 py-3 text-right">
            <p class="text-xs text-gray-500">Total encaissé</p>
            <p class="text-lg font-bold text-hb-blue"><?php echo e(number_format($total, 0, ',', ' ')); ?> FCFA</p>
        </div>
    <?php endif; ?>
</div>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[720px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Référence</th>
                <th class="px-5 py-3">Réservation</th>
                <th class="px-5 py-3">Méthode</th>
                <th class="px-5 py-3">Montant</th>
                <th class="px-5 py-3">Commission</th>
                <th class="px-5 py-3">Statut</th>
                <th class="px-5 py-3">Date</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-hb-cream/50">
                    <td class="px-5 py-3 font-mono text-xs text-gray-600"><?php echo e($transaction->reference); ?></td>
                    <td class="px-5 py-3 text-gray-600">#<?php echo e($transaction->reservation_id); ?></td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($transaction->method); ?></td>
                    <td class="px-5 py-3 font-semibold text-hb-blue"><?php echo e(number_format($transaction->amount, 0, ',', ' ')); ?> FCFA</td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e(number_format($transaction->commission, 0, ',', ' ')); ?> FCFA</td>
                    <td class="px-5 py-3">
                        <span class="badge-pill <?php echo e(match($transaction->status) { 'reussie' => 'bg-green-100 text-green-700', 'en_attente' => 'bg-yellow-100 text-yellow-700', 'echouee' => 'bg-red-100 text-red-700', 'remboursee' => 'bg-gray-100 text-gray-600', default => 'bg-gray-100 text-gray-600' }); ?>">
                            <?php echo e($transaction->status); ?>

                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e($transaction->created_at->format('d/m/Y H:i')); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="7" class="px-5 py-8 text-center text-gray-400">Aucune transaction.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <?php echo e($transactions->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/admin/transactions.blade.php ENDPATH**/ ?>