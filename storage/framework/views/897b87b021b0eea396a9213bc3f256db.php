
<?php
    $hasActiveFilters = collect($fields)->pluck('name')->contains(fn ($n) => request()->query($n));
?>

<form method="GET" action="<?php echo e($action); ?>" class="card mb-6 flex flex-col gap-3 p-4 sm:flex-row sm:items-end sm:flex-wrap">
    <div class="flex flex-1 flex-col gap-3 sm:flex-row sm:flex-wrap">
        <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $field): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php if(($field['type'] ?? 'text') === 'text'): ?>
                <div class="<?php echo e($field['class'] ?? 'sm:min-w-[220px] sm:flex-1'); ?>">
                    <label class="label"><?php echo e($field['label']); ?></label>
                    <div class="relative">
                        <i class="bi <?php echo e($field['icon'] ?? 'bi-search'); ?> pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" name="<?php echo e($field['name']); ?>" value="<?php echo e(request($field['name'])); ?>"
                               placeholder="<?php echo e($field['placeholder'] ?? ''); ?>" class="input-field !pl-9">
                    </div>
                </div>
            <?php elseif(($field['type'] ?? '') === 'select'): ?>
                <div class="<?php echo e($field['class'] ?? ''); ?>">
                    <label class="label"><?php echo e($field['label']); ?></label>
                    <select name="<?php echo e($field['name']); ?>" class="input-field">
                        <?php $__currentLoopData = $field['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($value); ?>" <?php if(request($field['name']) === (string) $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="flex items-center gap-2 sm:shrink-0">
        <button type="submit" class="btn-primary !px-4 !py-2.5 text-sm">
            <i class="bi bi-funnel"></i> Filtrer
        </button>
        <?php if($hasActiveFilters): ?>
            <a href="<?php echo e($action); ?>" class="btn-outline !px-4 !py-2.5 text-sm">
                <i class="bi bi-x-circle"></i> Réinitialiser
            </a>
        <?php endif; ?>
    </div>
</form>
<?php /**PATH C:\Project\hotels-benin-new\resources\views\backoffice\partials\filters.blade.php ENDPATH**/ ?>