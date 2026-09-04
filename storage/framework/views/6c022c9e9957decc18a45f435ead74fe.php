

<?php $__env->startSection('title', 'Modifier la chambre — ' . $room->name); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('hote.rooms.index', $establishment->id)); ?>" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange hover:underline"><i class="bi bi-arrow-left"></i> Retour aux chambres</a>
    <h1 class="font-display text-3xl font-bold text-hb-blue">Modifier la chambre</h1>
    <p class="mt-1 text-sm text-gray-500">Vos modifications seront appliquées après approbation par un administrateur.</p>
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

<form action="<?php echo e(route('hote.rooms.update', [$establishment->id, $room->id])); ?>" method="POST" enctype="multipart/form-data" class="card p-6 space-y-4">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <h2 class="flex items-center gap-2 font-display text-lg font-bold text-hb-blue"><i class="bi bi-door-open"></i> <?php echo e($room->name); ?></h2>

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

    <div>
        <label class="label">Image de couverture de la chambre (fichier)</label>
        <?php if($room->cover_image): ?>
            <img src="<?php echo e($room->cover_image); ?>" alt="Chambre" class="mb-2 h-32 w-full rounded-xl object-cover">
        <?php endif; ?>
        <input type="file" name="cover_image_file" accept="image/*" class="input-field !py-2">
        <p class="mt-1 text-xs text-gray-400">JPG, PNG ou WebP — 5 Mo max. Laissez vide pour conserver l'image actuelle.</p>
    </div>
    <div>
        <label class="label">Ou URL de l'image de couverture</label>
        <input type="url" name="cover_image" value="<?php echo e(old('cover_image', $room->cover_image)); ?>" class="input-field">
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Enregistrer (en attente d'approbation)</button>
        <a href="<?php echo e(route('hote.rooms.index', $establishment->id)); ?>" class="btn-outline">Annuler</a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views\backoffice\hote\edit-room.blade.php ENDPATH**/ ?>