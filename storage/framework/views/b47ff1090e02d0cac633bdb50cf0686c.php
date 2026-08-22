<?php $__env->startSection('title', 'Mes avis — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section>
    <nav class="mb-6 flex items-center gap-1 text-sm text-gray-500">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="<?php echo e(route('reservations.dashboard')); ?>" class="hover:text-hb-blue">Espace voyageur</a> <span>/</span>
        <span class="font-semibold text-hb-blue">Mes avis</span>
    </nav>

    <div class="mb-8">
        <h1 class="font-display text-3xl font-bold text-hb-blue">Mes avis</h1>
        <p class="mt-1 text-sm text-gray-500">Les avis que vous avez laissés sur les chambres et établissements. Vous pouvez les supprimer à tout moment.</p>
    </div>

    <?php $__empty_1 = true; $__currentLoopData = $reviews; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $review): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card mb-4 p-5 sm:p-6">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div class="min-w-0">
                    <p class="font-semibold text-hb-blue"><?php echo e($review->establishment->name); ?>

                        <?php if($review->room): ?>
                            <span class="text-sm font-normal text-gray-500">· <?php echo e($review->room->name); ?></span>
                        <?php endif; ?>
                    </p>
                    <p class="mt-1 text-xs text-gray-400"><?php echo e($review->created_at->format('d/m/Y à H:i')); ?></p>
                </div>
                <div class="flex items-center gap-3">
                    <span class="text-lg text-hb-gold"><?php echo e(str_repeat('★', $review->rating)); ?><?php echo e(str_repeat('☆', 5 - $review->rating)); ?></span>
                    <form action="<?php echo e(route('reservations.reviews.destroy', $review)); ?>" method="POST"
                          onsubmit="return confirm('Supprimer cet avis ?');">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-trash"></i> Supprimer</button>
                    </form>
                </div>
            </div>
            <?php if($review->title): ?>
                <p class="mt-3 font-semibold text-gray-700"><?php echo e($review->title); ?></p>
            <?php endif; ?>
            <p class="mt-1 text-sm text-gray-600"><?php echo e($review->comment); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="card p-10 text-center">
            <p class="text-lg font-semibold text-hb-blue">Vous n'avez pas encore laissé d'avis.</p>
            <p class="mt-2 text-sm text-gray-500">Après votre séjour, partagez votre expérience depuis la page de l'établissement.</p>
            <a href="<?php echo e(route('establishments.index')); ?>" class="btn-gold mt-5"><i class="bi bi-search"></i> Découvrir des établissements</a>
        </div>
    <?php endif; ?>

    <div class="mt-6">
        <?php echo e($reviews->links()); ?>

    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.voyageur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/reservations/my-reviews.blade.php ENDPATH**/ ?>