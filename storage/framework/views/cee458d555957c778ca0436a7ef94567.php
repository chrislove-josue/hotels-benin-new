


<?php $__env->startSection('title', 'Modifier l\'établissement — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('admin.establishments')); ?>" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange hover:underline"><i class="bi bi-arrow-left"></i> Retour aux établissements</a>
    <h1 class="font-display text-3xl font-bold text-hb-blue">Modifier l'établissement</h1>
    <p class="mt-1 text-sm text-gray-500">Modification directe appliquée immédiatement sur la vitrine.</p>
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

<form action="<?php echo e(route('admin.establishments.update', $establishment->id)); ?>" method="POST" enctype="multipart/form-data" class="card p-6 space-y-4">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>

    <h2 class="flex items-center gap-2 font-display text-lg font-bold text-hb-blue"><i class="bi bi-building"></i> <?php echo e($establishment->name); ?></h2>

    <div>
        <label class="label">Nom *</label>
        <input type="text" name="name" value="<?php echo e(old('name', $establishment->name)); ?>" class="input-field" required>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="label">Type *</label>
            <select name="establishment_type_id" class="input-field" required>
                <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($type->id); ?>" <?php if($establishment->establishment_type_id == $type->id): echo 'selected'; endif; ?>><?php echo e($type->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label">Localité *</label>
            <select name="locality_id" class="input-field" required>
                <?php $__currentLoopData = $localities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($locality->id); ?>" <?php if($establishment->locality_id == $locality->id): echo 'selected'; endif; ?>><?php echo e($locality->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </div>
    <div>
        <label class="label">Adresse *</label>
        <input type="text" name="address" value="<?php echo e(old('address', $establishment->address)); ?>" class="input-field" required>
    </div>
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="label">Prix / nuit (FCFA) *</label>
            <input type="number" name="price_from" value="<?php echo e(old('price_from', $establishment->price_from)); ?>" class="input-field" min="0" required>
        </div>
        <div>
            <label class="label">Capacité (pers.) *</label>
            <input type="number" name="capacity" value="<?php echo e(old('capacity', $establishment->capacity)); ?>" class="input-field" min="1" required>
        </div>
    </div>
<div>
        <label class="label">Image de couverture (fichier)</label>
        <?php if($establishment->cover_image): ?>
            <img src="<?php echo e($establishment->cover_image); ?>" alt="Couverture" class="mb-2 h-32 w-full rounded-xl object-cover">
        <?php endif; ?>
        <input type="file" name="cover_image_file" accept="image/*" class="input-field !py-2">
        <p class="mt-1 text-xs text-gray-400">JPG, PNG ou WebP — 5 Mo max. Laissez vide pour conserver l'image actuelle.</p>
    </div>
    <div>
        <label class="label">Ou URL de l'image de couverture</label>
        <input type="url" name="cover_image" value="<?php echo e(old('cover_image', $establishment->cover_image)); ?>" class="input-field">
    </div>
    <div>
        <label class="label">Ajouter des photos à la galerie</label>
        <input type="file" name="images[]" accept="image/*" multiple class="input-field !py-2">
        <?php if($establishment->images->isNotEmpty()): ?>
            <div class="mt-2 flex flex-wrap gap-2">
                <?php $__currentLoopData = $establishment->images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <img src="<?php echo e($image->path); ?>" alt="" class="h-16 w-20 rounded-lg object-cover">
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
            <p class="mt-1 text-xs text-gray-400">Les nouvelles photos seront ajoutées à cette galerie.</p>
        <?php else: ?>
            <p class="mt-1 text-xs text-gray-400">Aucune photo de galerie pour le moment.</p>
        <?php endif; ?>
    </div>
    <div>
        <label class="label">Courte description *</label>
        <input type="text" name="short_description" value="<?php echo e(old('short_description', $establishment->short_description)); ?>" class="input-field" required>
    </div>
    <div>
        <label class="label">Description *</label>
        <textarea name="description" rows="5" class="input-field" required><?php echo e(old('description', $establishment->description)); ?></textarea>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
            <input type="checkbox" name="is_available" value="1" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange" <?php if($establishment->is_available): echo 'checked'; endif; ?>>
            Disponible sur la vitrine
        </label>
        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
            <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 rounded text-hb-gold focus:ring-hb-gold" <?php if($establishment->is_featured): echo 'checked'; endif; ?>>
            <i class="bi bi-star-fill text-hb-gold"></i> À la une
        </label>
    </div>

    <div>
        <label class="label">Équipements</label>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
            <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:border-hb-blue">
                    <input type="checkbox" name="amenities[]" value="<?php echo e($amenity->id); ?>" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange"
                        <?php if($establishment->amenities->contains($amenity->id)): echo 'checked'; endif; ?>>
                    <?php echo e($amenity->name); ?>

                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Enregistrer (appliqué immédiatement)</button>
        <a href="<?php echo e(route('admin.establishments')); ?>" class="btn-outline">Annuler</a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/admin/edit-establishment.blade.php ENDPATH**/ ?>