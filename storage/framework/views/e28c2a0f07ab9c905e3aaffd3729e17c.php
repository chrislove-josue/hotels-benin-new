<?php $__env->startSection('title', 'Avis — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Avis clients</h1>
    <p class="mt-1 text-sm text-gray-500">Tous les avis sur les chambres et établissements — modération possible.</p>
</div>


<div class="mb-6 grid grid-cols-2 gap-4 lg:grid-cols-4">
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-blue/10 text-hb-blue"><i class="bi bi-chat-square-text"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue"><?php echo e($stats['total']); ?></p>
            <p class="text-xs text-gray-500">Avis au total</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-gold/15 text-hb-gold"><i class="bi bi-star-fill"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue"><?php echo e(number_format($stats['avg'], 2)); ?> / 5</p>
            <p class="text-xs text-gray-500">Note moyenne</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-orange/10 text-hb-orange"><i class="bi bi-door-open"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue"><?php echo e($stats['rooms']); ?></p>
            <p class="text-xs text-gray-500">Sur chambres</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-green-100 text-green-600"><i class="bi bi-building"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue"><?php echo e($stats['establishments']); ?></p>
            <p class="text-xs text-gray-500">Sur établissements</p>
        </div>
    </div>
</div>

<?php echo $__env->make('backoffice.partials.filters', [
    'action' => route('admin.avis'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Voyageur, établissement, contenu…'],
        ['name' => 'note', 'label' => 'Note', 'type' => 'select',
         'options' => ['' => 'Toutes les notes', '5' => '★ 5', '4' => '★ 4', '3' => '★ 3', '2' => '★ 2', '1' => '★ 1']],
        ['name' => 'type', 'label' => 'Type', 'type' => 'select',
         'options' => ['' => 'Tous les types', 'chambre' => 'Chambres', 'etablissement' => 'Établissements']],
    ],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="card overflow-x-auto">
    <table class="w-full min-w-[900px] text-left text-sm">
        <thead class="border-b border-gray-100 bg-hb-cream text-xs uppercase tracking-wide text-gray-500">
            <tr>
                <th class="px-5 py-3">Voyageur</th>
                <th class="px-5 py-3">Cible</th>
                <th class="px-5 py-3">Note</th>
                <th class="px-5 py-3">Avis</th>
                <th class="px-5 py-3">Date</th>
                <th class="px-5 py-3 text-right">Action</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-50">
            <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-hb-cream/50 align-top">
                    <td class="px-5 py-3">
                        <p class="font-semibold text-hb-blue"><?php echo e($review->user->name); ?></p>
                        <p class="text-xs text-gray-400"><?php echo e($review->user->email); ?></p>
                    </td>
                    <td class="px-5 py-3">
                        <p class="text-gray-700"><?php echo e($review->establishment->name); ?></p>
                        <?php if($review->room): ?>
                            <p class="text-xs text-gray-400"><i class="bi bi-door-open"></i> <?php echo e($review->room->name); ?></p>
                        <?php else: ?>
                            <p class="text-xs text-gray-400">Établissement général</p>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-hb-gold">
                        <?php echo e(str_repeat('★', $review->rating)); ?><?php echo e(str_repeat('☆', 5 - $review->rating)); ?>

                    </td>
                    <td class="px-5 py-3 max-w-md">
                        <?php if($review->title): ?>
                            <p class="font-semibold text-gray-700"><?php echo e($review->title); ?></p>
                        <?php endif; ?>
                        <p class="mt-0.5 text-gray-600"><?php echo e(\Illuminate\Support\Str::limit($review->comment, 160)); ?></p>
                    </td>
                    <td class="px-5 py-3 whitespace-nowrap text-xs text-gray-500"><?php echo e($review->created_at->format('d/m/Y H:i')); ?></td>
                    <td class="px-5 py-3">
                        <form action="<?php echo e(route('admin.avis.destroy', $review->id)); ?>" method="POST"
                              onsubmit="return confirm('Supprimer cet avis ? Cette action est définitive.');">
                            <?php echo csrf_field(); ?>
                            <?php echo method_field('DELETE'); ?>
                            <button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-trash"></i> Supprimer</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="px-5 py-8 text-center text-gray-400">Aucun avis.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<div class="mt-6">
    <?php echo e($reviews->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/admin/reviews.blade.php ENDPATH**/ ?>