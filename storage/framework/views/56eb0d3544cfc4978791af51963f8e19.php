

<?php $__env->startSection('title', 'Modifier la chambre — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('admin.establishments.rooms', $establishment->id)); ?>" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange hover:underline"><i class="bi bi-arrow-left"></i> Retour aux chambres</a>
    <h1 class="font-display text-3xl font-bold text-hb-blue">Modifier la chambre</h1>
    <p class="mt-1 text-sm text-gray-500">Modification directe appliquée immédiatement.</p>
</div>

<?php if($errors->any()): ?>
    <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">
        <ul class="list-inside list-disc space-y-1">
            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li><?php echo e($error); ?></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </ul>
    </div>
<?php endif; ?>

<form action="<?php echo e(route('admin.rooms.update', [$establishment->id, $room->id])); ?>" method="POST" class="card p-6 space-y-4">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <h2 class="flex items-center gap-2 font-display text-lg font-bold text-hb-blue"><i class="bi bi-door-open"></i> <?php echo e($room->name); ?></h2>
    <p class="text-sm text-gray-500">Établissement : <span class="font-semibold text-hb-blue"><?php echo e($establishment->name); ?></span></p>

    <div>
        <label class="label">Nom *</label>
        <input type="text" name="name" value="<?php echo e(old('name', $room->name)); ?>" class="input-field" required>
    </div>
    <div>
        <label class="label">Description</label>
        <input type="text" name="description" value="<?php echo e(old('description', $room->description)); ?>" class="input-field">
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <label class="label">Capacité *</label>
            <input type="number" name="capacity" value="<?php echo e(old('capacity', $room->capacity)); ?>" class="input-field" min="1" required>
        </div>
        <div>
            <label class="label">Prix / nuit *</label>
            <input type="number" name="price_per_night" value="<?php echo e(old('price_per_night', $room->price_per_night)); ?>" class="input-field" min="0" required>
        </div>
        <div>
            <label class="label">Quantité *</label>
            <input type="number" name="quantity" value="<?php echo e(old('quantity', $room->quantity)); ?>" class="input-field" min="1" required>
        </div>
    </div>

    <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
        <input type="checkbox" name="is_available" value="1" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange" <?php if($room->is_available): echo 'checked'; endif; ?>>
        Disponible
    </label>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Enregistrer (appliqué immédiatement)</button>
        <a href="<?php echo e(route('admin.establishments.rooms', $establishment->id)); ?>" class="btn-outline">Annuler</a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views\backoffice\admin\edit-room.blade.php ENDPATH**/ ?>