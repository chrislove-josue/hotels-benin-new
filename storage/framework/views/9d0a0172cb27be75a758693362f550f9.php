<?php $__env->startSection('title', 'Avis — Espace hôte'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Avis reçus</h1>
    <p class="mt-1 text-sm text-gray-500">Ce que les voyageurs disent de vos établissements et chambres.</p>
</div>


<div class="mb-6 grid grid-cols-2 gap-4">
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-blue/10 text-hb-blue"><i class="bi bi-chat-square-text"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue"><?php echo e($stats['total']); ?></p>
            <p class="text-xs text-gray-500">Avis reçus</p>
        </div>
    </div>
    <div class="stat-card">
        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-hb-gold/15 text-hb-gold"><i class="bi bi-star-fill"></i></div>
        <div>
            <p class="text-xl font-bold text-hb-blue"><?php echo e(number_format($stats['avg'], 2)); ?> / 5</p>
            <p class="text-xs text-gray-500">Note moyenne globale</p>
        </div>
    </div>
</div>

<?php echo $__env->make('backoffice.partials.filters', [
    'action' => route('hote.avis'),
    'fields' => [
        ['name' => 'q', 'label' => 'Recherche', 'placeholder' => 'Voyageur, contenu…'],
        ['name' => 'note', 'label' => 'Note', 'type' => 'select',
         'options' => ['' => 'Toutes les notes', '5' => '★ 5', '4' => '★ 4', '3' => '★ 3', '2' => '★ 2', '1' => '★ 1']],
        ['name' => 'etablissement', 'label' => 'Établissement', 'type' => 'select',
         'options' => ['' => 'Tous mes établissements'] + $establishments->pluck('name', 'id')->all()],
    ],
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<div class="space-y-4">
    <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-gradient-to-br from-hb-blue to-hb-blue-deeper font-bold text-hb-gold"><?php echo e(strtoupper(substr($review->user->name, 0, 1))); ?></span>
                    <div class="min-w-0">
                        <p class="font-semibold text-hb-blue"><?php echo e($review->user->name); ?></p>
                        <p class="text-xs text-gray-400">
                            <?php echo e($review->establishment->name); ?>

                            <?php if($review->room): ?>
                                · <?php echo e($review->room->name); ?>

                            <?php endif; ?>
                            · <?php echo e($review->created_at->diffForHumans()); ?>

                        </p>
                    </div>
                </div>
                <span class="whitespace-nowrap text-lg text-hb-gold"><?php echo e(str_repeat('★', $review->rating)); ?><?php echo e(str_repeat('☆', 5 - $review->rating)); ?></span>
            </div>
            <?php if($review->title): ?>
                <p class="mt-3 font-semibold text-gray-700"><?php echo e($review->title); ?></p>
            <?php endif; ?>
            <p class="mt-1 text-sm text-gray-600"><?php echo e($review->comment); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="card p-10 text-center">
            <p class="text-lg font-semibold text-hb-blue">Aucun avis pour le moment.</p>
            <p class="mt-1 text-sm text-gray-500">Les avis de vos voyageurs s'afficheront ici après leurs séjours.</p>
        </div>
    <?php endif; ?>
</div>

<div class="mt-6">
    <?php echo e($reviews->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/hote/reviews.blade.php ENDPATH**/ ?>