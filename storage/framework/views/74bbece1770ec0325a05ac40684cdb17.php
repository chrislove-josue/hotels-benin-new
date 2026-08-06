

<?php $__env->startSection('title', 'Gérer l\'établissement — Espace hôte'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue"><?php echo e($establishment->name); ?></h1>
    <p class="mt-1 text-sm text-gray-500">Modifiez les informations et gérez les chambres / espaces.</p>
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

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    
    <form action="<?php echo e(route('hote.establishments.update', $establishment->id)); ?>" method="POST" class="card p-6 space-y-4">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <h2 class="font-display text-lg font-bold text-hb-blue">Informations</h2>

        <div>
            <label class="label">Nom *</label>
            <input type="text" name="name" value="<?php echo e(old('name', $establishment->name)); ?>" class="input-field" required>
        </div>
        <div class="grid grid-cols-2 gap-3">
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
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Prix / nuit *</label>
                <input type="number" name="price_from" value="<?php echo e(old('price_from', $establishment->price_from)); ?>" class="input-field" min="0" required>
            </div>
            <div>
                <label class="label">Capacité *</label>
                <input type="number" name="capacity" value="<?php echo e(old('capacity', $establishment->capacity)); ?>" class="input-field" min="1" required>
            </div>
        </div>
        <div>
            <label class="label">Image de couverture (URL)</label>
            <input type="url" name="cover_image" value="<?php echo e(old('cover_image', $establishment->cover_image)); ?>" class="input-field">
        </div>
        <div>
            <label class="label">Courte description *</label>
            <input type="text" name="short_description" value="<?php echo e(old('short_description', $establishment->short_description)); ?>" class="input-field" required>
        </div>
        <div>
            <label class="label">Description *</label>
            <textarea name="description" rows="5" class="input-field" required><?php echo e(old('description', $establishment->description)); ?></textarea>
        </div>

        <div>
            <label class="label">Équipements</label>
            <div class="grid grid-cols-2 gap-2 sm:grid-cols-3">
                <?php $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 hover:border-hb-blue">
                        <input type="checkbox" name="amenities[]" value="<?php echo e($amenity->id); ?>" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange"
                            <?php if($establishment->amenities->contains($amenity->id)): echo 'checked'; endif; ?>>
                        <?php echo e($amenity->name); ?>

                    </label>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

<button type="submit" class="btn-primary w-full"><i class="bi bi-check-lg"></i> Enregistrer les modifications</button>
    </form>

    
    <div class="space-y-6">
        <div class="card p-6">
            <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Ajouter une chambre / un espace</h2>
            <form action="<?php echo e(route('hote.rooms.store', $establishment->id)); ?>" method="POST" class="space-y-3">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="label">Nom *</label>
                    <input type="text" name="name" class="input-field" placeholder="Ex : Chambre double" required>
                </div>
                <div>
                    <label class="label">Description</label>
                    <input type="text" name="description" class="input-field">
                </div>
                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="label">Capacité *</label>
                        <input type="number" name="capacity" class="input-field" min="1" value="2" required>
                    </div>
                    <div>
                        <label class="label">Prix / nuit *</label>
                        <input type="number" name="price_per_night" class="input-field" min="0" required>
                    </div>
                    <div>
                        <label class="label">Quantité *</label>
                        <input type="number" name="quantity" class="input-field" min="1" value="1" required>
                    </div>
                </div>
<button type="submit" class="btn-gold w-full"><i class="bi bi-plus-lg"></i> Ajouter la chambre</button>
            </form>
        </div>

        <div class="card">
            <div class="border-b border-gray-100 px-5 py-4">
                <h2 class="font-display text-lg font-bold text-hb-blue">Chambres existantes (<?php echo e($establishment->rooms->count()); ?>)</h2>
            </div>
            <div class="divide-y divide-gray-50">
                <?php $__empty_1 = true; $__currentLoopData = $establishment->rooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $room): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="font-semibold text-hb-blue"><?php echo e($room->name); ?></p>
                                <p class="text-xs text-gray-500"><?php echo e($room->description); ?></p>
                                <p class="mt-1 text-xs text-gray-400">Capacité <?php echo e($room->capacity); ?> pers. · <span class="font-semibold text-hb-blue"><?php echo e(number_format($room->price_per_night, 0, ',', ' ')); ?> FCFA</span> / nuit</p>
                            </div>
                            <form action="<?php echo e(route('hote.rooms.destroy', [$establishment->id, $room->id])); ?>" method="POST" onsubmit="return confirm('Supprimer cette chambre ?');">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
<button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-trash"></i> Supprimer</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <p class="px-5 py-6 text-center text-sm text-gray-400">Aucune chambre pour le moment.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views\backoffice\hote\edit-establishment.blade.php ENDPATH**/ ?>