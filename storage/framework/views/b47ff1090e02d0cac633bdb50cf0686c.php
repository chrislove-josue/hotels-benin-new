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
                    <button type="button" class="btn-outline !px-3 !py-1.5 text-xs !border-hb-blue/30 !text-hb-blue hover:!bg-hb-blue hover:!text-white"
                            x-data
                            @click="$dispatch('open-edit', { id: <?php echo e($review->id); ?>, rating: <?php echo e($review->rating); ?>, title: <?php echo e(\Illuminate\Support\Js::from($review->title)); ?>, comment: <?php echo e(\Illuminate\Support\Js::from($review->comment)); ?> })">
                        <i class="bi bi-pencil-square"></i> Modifier
                    </button>
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


<div x-data="{ open: false, id: null, rating: 5, title: '', comment: '' }"
     @open-edit.window="open = true; id = $event.detail.id; rating = $event.detail.rating; title = $event.detail.title ?? ''; comment = $event.detail.comment"
     x-cloak>
    <div x-show="open" x-transition.opacity class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm" @click="open = false"></div>
    <div x-show="open" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4"
         @keydown.escape.window="open = false">
        <div class="w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl" @click.stop>
            <h2 class="font-display text-xl font-bold text-hb-blue"><i class="bi bi-pencil-square text-hb-gold"></i> Modifier mon avis</h2>

            <form :action="'<?php echo e(url('espace-voyageur/mes-avis')); ?>/' + id" method="POST" class="mt-5 space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-hb-blue">Note</label>
                    <select name="rating" x-model="rating" required
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-hb-gold focus:outline-none">
                        <?php for($i = 5; $i >= 1; $i--): ?>
                            <option value="<?php echo e($i); ?>"><?php echo e(str_repeat('★', $i)); ?> (<?php echo e($i); ?>/5)</option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-hb-blue">Titre <span class="font-normal text-gray-400">(optionnel)</span></label>
                    <input type="text" name="title" x-model="title" maxlength="120"
                           class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-hb-gold focus:outline-none">
                </div>

                <div>
                    <label class="mb-1 block text-sm font-semibold text-hb-blue">Commentaire</label>
                    <textarea name="comment" x-model="comment" rows="4" required maxlength="2000"
                              class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-hb-gold focus:outline-none"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit" class="btn-gold flex-1"><i class="bi bi-check-lg"></i> Enregistrer</button>
                    <button type="button" class="btn-outline flex-1" @click="open = false">Annuler</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.voyageur', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/reservations/my-reviews.blade.php ENDPATH**/ ?>