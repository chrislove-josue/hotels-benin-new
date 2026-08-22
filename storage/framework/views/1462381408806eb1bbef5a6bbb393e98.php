<?php $__env->startSection('title', 'Transactions — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <div>
        <h1 class="font-display text-3xl font-bold text-hb-blue">Transactions</h1>
        <p class="mt-1 text-sm text-gray-500">Historique des paiements et commissions — mise à jour manuelle possible (espèces, remboursement).</p>
    </div>
    <?php if($total): ?>
        <div class="card px-5 py-3 text-right">
            <p class="text-xs text-gray-500">Total encaissé</p>
            <p class="text-lg font-bold text-hb-blue"><?php echo e(number_format($total, 0, ',', ' ')); ?> FCFA</p>
        </div>
    <?php endif; ?>
</div>

<?php if(session('status')): ?>
    <div class="mb-4 rounded-xl bg-emerald-50 p-4 text-sm font-medium text-emerald-700">
        <?php echo e(session('status')); ?>

    </div>
<?php endif; ?>

<?php
    $txLabels = ['reussie' => 'Réussie', 'en_attente' => 'En attente', 'echouee' => 'Échouée', 'remboursee' => 'Remboursée'];
    $txStyles = [
        'reussie' => 'bg-green-100 text-green-700',
        'en_attente' => 'bg-yellow-100 text-yellow-700',
        'echouee' => 'bg-red-100 text-red-700',
        'remboursee' => 'bg-purple-100 text-purple-700',
    ];
    $methodLabels = [
        '' => 'Toutes les méthodes',
        'fedapay' => 'FedaPay (mobile money / carte)',
        'moov_money' => 'Moov Money',
        'mtn_money' => 'MTN MoMo',
        'sur_place' => 'Sur place (espèces)',
    ];
?>

<?php echo $__env->make('backoffice.partials.filters', [
    'action' => route('admin.transactions'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Référence transaction ou réservation…'],
        ['name' => 'statut', 'label' => 'Statut', 'type' => 'select', 'options' => ['' => 'Tous les statuts'] + $txLabels],
        ['name' => 'methode', 'label' => 'Méthode', 'type' => 'select', 'options' => $methodLabels],
    ],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[980px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Référence</th>
                <th class="px-5 py-3">Réservation / Voyageur</th>
                <th class="px-5 py-3">Méthode</th>
                <th class="px-5 py-3">Montant</th>
                <th class="px-5 py-3">Commission</th>
                <th class="px-5 py-3">Statut &amp; raison</th>
                <th class="px-5 py-3">Date</th>
                <th class="px-5 py-3">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="align-top hover:bg-hb-cream/50">
                    <td class="px-5 py-3 font-mono text-xs text-gray-600"><?php echo e($transaction->reference); ?></td>
                    <td class="px-5 py-3">
                        <p class="font-semibold text-hb-blue"><?php echo e($transaction->reservation?->reference ?? '#' . $transaction->reservation_id); ?></p>
                        <p class="text-xs text-gray-500"><?php echo e($transaction->reservation?->establishment?->name); ?></p>
                        <p class="text-xs text-gray-400"><?php echo e($transaction->reservation?->user?->name); ?></p>
                    </td>
                    <td class="px-5 py-3 capitalize text-gray-600"><?php echo e($transaction->method ?? '—'); ?></td>
                    <td class="px-5 py-3 font-semibold text-hb-blue"><?php echo e(number_format($transaction->amount, 0, ',', ' ')); ?> FCFA</td>
                    <td class="px-5 py-3 text-gray-600"><?php echo e(number_format($transaction->commission, 0, ',', ' ')); ?> FCFA</td>
                    <td class="px-5 py-3">
                        <span class="badge-pill <?php echo e($txStyles[$transaction->status] ?? 'bg-gray-100 text-gray-600'); ?>">
                            <?php echo e($txLabels[$transaction->status] ?? $transaction->status); ?>

                        </span>
                        <?php if($transaction->reason): ?>
                            <p class="mt-1 max-w-[200px] text-[11px] italic text-gray-500" title="<?php echo e($transaction->reason); ?>">« <?php echo e($transaction->reason); ?> »</p>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-xs text-gray-600"><?php echo e($transaction->created_at->format('d/m/Y H:i')); ?></td>
                    <td class="px-5 py-3">
                        <form action="<?php echo e(route('admin.transactions.update', $transaction->id)); ?>" method="POST" class="flex w-64 flex-col gap-2" x-data="{ status: '<?php echo e($transaction->status); ?>' }">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('PATCH'); ?>
                            <select name="status" x-model="status" class="input-field !py-1.5 text-xs">
                                <?php $__currentLoopData = $txLabels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php if($transaction->status === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <input type="text" name="reason" value="<?php echo e($transaction->reason); ?>" placeholder="Raison (ex : payé en espèces)" maxlength="255"
                                   class="input-field !py-1.5 text-xs"
                                   :required="['reussie','echouee','remboursee'].includes(status)">
                            <button type="submit" class="btn-primary !py-1.5 text-xs"><i class="bi bi-save"></i> Mettre à jour</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr>
                    <td colspan="8" class="px-5 py-8 text-center text-gray-400">Aucune transaction.</td>
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