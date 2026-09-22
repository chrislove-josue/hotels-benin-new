

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

<div class="grid grid-cols-1 gap-6">
    
<form action="<?php echo e(route('hote.establishments.update', $establishment->id)); ?>" method="POST" enctype="multipart/form-data" class="card p-6 space-y-4">
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
                <?php echo $__env->make('backoffice.partials.locality-picker', [
                    'localities' => $localities,
                    'selected' => old('locality_id', $establishment->locality_id),
                    'addNewInit' => old('new_locality') ? 'true' : 'false',
                    'newLocalityValue' => old('new_locality'),
                ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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
        <div class="grid grid-cols-2 gap-3">
            <div>
                <label class="label">Commission plateforme (%)</label>
                <select name="commission_pct" class="input-field">
                    <?php $__currentLoopData = [15, 20, 25, 30]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if((float) old('commission_pct', $establishment->commission_pct) == $value): echo 'selected'; endif; ?>><?php echo e($value); ?>%</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="mt-1 text-xs text-gray-400">Part prélevée sur chaque réservation.</p>
            </div>
            <div></div>
        </div>
        <div>
            <?php echo $__env->make('backoffice.partials.location-picker', ['lat' => old('latitude', $establishment->latitude), 'lng' => old('longitude', $establishment->longitude)], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
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

        <div x-data="{ transferOn: <?php echo e($establishment->transfer_available ? 'true' : 'false'); ?> }">
            <h3 class="mb-3 font-display text-sm font-bold text-hb-blue"><i class="bi bi-car-front"></i> Service automobile (transfert aéroport / navette)</h3>
            <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 transition hover:border-hb-blue">
                <input type="checkbox" name="transfer_available" value="1" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange" x-model="transferOn" <?php if($establishment->transfer_available): echo 'checked'; endif; ?>>
                <span class="text-sm font-semibold text-gray-700">Je propose un service d'accueil / transfert pour les voyageurs</span>
            </label>
            <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2" x-show="transferOn" x-transition>
                <div>
                    <label class="label">Prix du transfert (FCFA)</label>
                    <input type="number" name="transfer_fee" value="<?php echo e(old('transfer_fee', $establishment->transfer_fee)); ?>" class="input-field" min="0" placeholder="ex: 10000">
                </div>
                <div>
                    <label class="label">Description du trajet</label>
                    <input type="text" name="transfer_description" value="<?php echo e(old('transfer_description', $establishment->transfer_description)); ?>" class="input-field" maxlength="255" placeholder="ex: Aéroport Cadjèhoun → établissement">
                </div>
            </div>
            <p class="mt-1 text-xs text-gray-400" x-show="transferOn">Le voyageur verra cette information sur la fiche et pourra vous contacter directement via WhatsApp.</p>
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
            <h2 class="mb-2 flex items-center gap-2 font-display text-lg font-bold text-hb-blue"><i class="bi bi-door-open"></i> Chambres & espaces</h2>
            <p class="text-sm text-gray-500">Gérez les chambres de cet établissement : ajout, modification et suppression. Chaque changement doit être approuvé par un administrateur.</p>
            <div class="mt-4 flex items-center gap-3">
                <span class="badge-pill bg-hb-blue/10 text-hb-blue"><?php echo e($establishment->rooms->count()); ?> chambre(s)</span>
                <a href="<?php echo e(route('hote.rooms.index', $establishment->id)); ?>" class="btn-gold !px-4 !py-2 text-xs"><i class="bi bi-gear"></i> Gérer les chambres</a>
            </div>
        </div>

        <div class="card p-6">
            <h2 class="mb-2 flex items-center gap-2 font-display text-lg font-bold text-hb-blue"><i class="bi bi-info-circle"></i> Modération</h2>
            <p class="text-sm text-gray-500">Les modifications des informations de cet établissement sont mises en file d'attente et seront appliquées publiquement uniquement après approbation par un administrateur.</p>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/backoffice/hote/edit-establishment.blade.php ENDPATH**/ ?>