<?php $compact = $compact ?? false; ?>

<a href="<?php echo e(route('establishments.show', $establishment->slug)); ?>" class="card group flex flex-col">
    <div class="relative <?php echo e($compact ? 'h-40' : 'h-52'); ?> overflow-hidden">
        <img src="<?php echo e($establishment->cover_image); ?>" alt="<?php echo e($establishment->name); ?>"
             class="h-full w-full object-cover transition duration-700 group-hover:scale-110" loading="lazy">

        
        <div class="absolute inset-0 bg-gradient-to-t from-hb-blue-deeper/60 via-transparent to-transparent opacity-0 transition duration-500 group-hover:opacity-100"></div>

        <?php if($establishment->is_featured): ?>
            <span class="absolute left-3 top-3 rounded-full bg-gradient-to-r from-hb-orange to-hb-gold px-3 py-1 text-xs font-bold text-white shadow">Coup de cœur</span>
        <?php endif; ?>

        <span class="badge-white absolute right-3 top-3">
            <?php echo e($establishment->type->name); ?>

        </span>

        
        <div class="absolute bottom-3 left-3 translate-y-2 opacity-0 transition duration-500 group-hover:translate-y-0 group-hover:opacity-100">
            <span class="rounded-full bg-white/95 px-3 py-1 text-xs font-bold text-hb-blue-deeper shadow">
                à partir de <?php echo e(number_format($establishment->price_from, 0, ',', ' ')); ?> FCFA
            </span>
        </div>
    </div>

    <div class="flex flex-1 flex-col p-4">
        <div class="mb-1 flex items-center justify-between gap-2">
            <h3 class="font-display font-bold text-hb-blue-deeper <?php echo e($compact ? 'text-sm' : 'text-base'); ?> line-clamp-1">
                <?php echo e($establishment->name); ?>

            </h3>
            <span class="flex shrink-0 items-center gap-1 rounded-full bg-hb-gold/10 px-2 py-0.5 text-xs font-semibold text-hb-gold">
                ★ <?php echo e(number_format($establishment->rating, 1)); ?>

            </span>
        </div>

<p class="mb-3 flex items-center gap-1 text-xs text-gray-500">
            <i class="bi bi-geo-alt text-hb-orange"></i>
            <?php echo e($establishment->locality->name); ?>

        </p>

        <?php if(!$compact): ?>
            <p class="mb-4 flex-1 text-sm text-gray-600 line-clamp-2"><?php echo e($establishment->short_description); ?></p>
        <?php endif; ?>

        <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-3">
            <span class="text-xs text-gray-400">à partir de</span>
            <span class="font-display text-lg font-bold text-hb-blue">
                <?php echo e(number_format($establishment->price_from, 0, ',', ' ')); ?> <span class="text-xs font-medium">FCFA</span>
            </span>
        </div>
    </div>
</a>
<?php /**PATH C:\Project\hotels-benin-new\resources\views/partials/establishment-card.blade.php ENDPATH**/ ?>