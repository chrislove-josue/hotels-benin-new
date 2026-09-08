

<?php $__env->startSection('title', 'Nouvel établissement — Espace hôte'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Nouvel établissement</h1>
    <p class="mt-1 text-sm text-gray-500">Complétez les informations. L'établissement sera visible après validation par un administrateur.</p>
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

<form action="<?php echo e(route('hote.establishments.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
    <?php echo csrf_field(); ?>

    <div class="card p-6">
        <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Informations générales</h2>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="sm:col-span-2">
                <label class="label">Nom de l'établissement *</label>
                <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="input-field" required>
            </div>
            <div>
                <label class="label">Type d'établissement *</label>
                <select name="establishment_type_id" class="input-field" required>
                    <option value="">Sélectionner...</option>
                    <?php $__currentLoopData = $types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($type->id); ?>" <?php if(old('establishment_type_id') == $type->id): echo 'selected'; endif; ?>><?php echo e($type->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label class="label">Localité *</label>
                <select name="locality_id" class="input-field" required>
                    <option value="">Sélectionner...</option>
                    <?php $__currentLoopData = $localities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($locality->id); ?>" <?php if(old('locality_id') == $locality->id): echo 'selected'; endif; ?>><?php echo e($locality->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="sm:col-span-2">
                <label class="label">Adresse *</label>
                <input type="text" name="address" value="<?php echo e(old('address')); ?>" class="input-field" placeholder="Quartier, Ville" required>
            </div>
            <div>
                <label class="label">Prix à partir de (FCFA / nuit) *</label>
                <input type="number" name="price_from" value="<?php echo e(old('price_from')); ?>" class="input-field" min="0" required>
            </div>
            <div>
                <label class="label">Capacité (pers.) *</label>
                <input type="number" name="capacity" value="<?php echo e(old('capacity', 2)); ?>" class="input-field" min="1" required>
            </div>
            <div>
                <label class="label">Commission plateforme (%)</label>
                <select name="commission_pct" class="input-field">
                    <?php $__currentLoopData = [15, 20, 25, 30]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($value); ?>" <?php if((float) old('commission_pct', 25) == $value): echo 'selected'; endif; ?>><?php echo e($value); ?>%</option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
                <p class="mt-1 text-xs text-gray-400">Part prélevée sur chaque réservation.</p>
            </div>
            <div>
                <label class="label">Latitude</label>
                <input type="text" name="latitude" value="<?php echo e(old('latitude')); ?>" class="input-field" placeholder="ex: 6.4969">
            </div>
            <div>
                <label class="label">Longitude</label>
                <input type="text" name="longitude" value="<?php echo e(old('longitude')); ?>" class="input-field" placeholder="ex: 2.6289">
                <p class="mt-1 text-xs text-gray-400">Sert à afficher la localisation sur la carte. Trouvez les coordonnées sur <a href="https://www.google.com/maps" target="_blank" class="text-hb-blue underline">Google Maps</a> (clic droit → « Les coordonnées »).</p>
            </div>
</div>
    </div>

    <div class="card p-6">
        <h2 class="mb-4 font-display text-lg font-bold text-hb-blue"><i class="bi bi-image"></i> Photos</h2>
        <div class="space-y-4">
            <div>
                <label class="label">Image de couverture (fichier)</label>
                <input type="file" name="cover_image_file" accept="image/*" class="input-field !py-2">
                <p class="mt-1 text-xs text-gray-400">JPG, PNG ou WebP — 5 Mo max.</p>
            </div>
            <div>
                <label class="label">Ou URL de l'image de couverture</label>
                <input type="url" name="cover_image" value="<?php echo e(old('cover_image')); ?>" class="input-field" placeholder="https://...">
            </div>
            <div>
                <label class="label">Galerie d'images (plusieurs fichiers)</label>
                <input type="file" name="images[]" accept="image/*" multiple class="input-field !py-2">
                <p class="mt-1 text-xs text-gray-400">Vous pouvez sélectionner plusieurs photos. JPG, PNG ou WebP — 5 Mo max chacune.</p>
            </div>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Description</h2>
        <div class="space-y-4">
            <div>
                <label class="label">Courte description *</label>
                <input type="text" name="short_description" value="<?php echo e(old('short_description')); ?>" class="input-field" maxlength="255" required>
            </div>
            <div>
                <label class="label">Description complète *</label>
                <textarea name="description" rows="6" class="input-field" required><?php echo e(old('description')); ?></textarea>
            </div>
        </div>
    </div>

    <div class="card p-6" x-data="{ transferOn: <?php echo e(old('transfer_available') ? 'true' : 'false'); ?> }">
        <h2 class="mb-4 font-display text-lg font-bold text-hb-blue"><i class="bi bi-car-front"></i> Service automobile (transfert aéroport / navette)</h2>
        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 px-4 py-3 transition hover:border-hb-blue">
            <input type="checkbox" name="transfer_available" value="1" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange" x-model="transferOn">
            <span class="text-sm font-semibold text-gray-700">Je propose un service d'accueil / transfert pour les voyageurs</span>
        </label>
        <div class="mt-4 space-y-4" x-show="transferOn" x-transition>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <label class="label">Prix du transfert (FCFA)</label>
                    <input type="number" name="transfer_fee" value="<?php echo e(old('transfer_fee')); ?>" class="input-field" min="0" placeholder="ex: 10000">
                </div>
                <div>
                    <label class="label">Description du trajet</label>
                    <input type="text" name="transfer_description" value="<?php echo e(old('transfer_description')); ?>" class="input-field" maxlength="255" placeholder="ex: Aéroport Cadjèhoun → établissement, Climate 2000">
                </div>
            </div>
            <p class="text-xs text-gray-400">Le voyageur verra cette information sur la fiche et pourra vous contacter directement via WhatsApp.</p>
        </div>
    </div>

    <div class="card p-6">
        <h2 class="mb-4 font-display text-lg font-bold text-hb-blue">Équipements</h2>
        <div class="grid grid-cols-2 gap-2 sm:grid-cols-3 md:grid-cols-4">
            <?php $__empty_1 = true; $__currentLoopData = $amenities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $amenity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-3 py-2 text-sm text-gray-700 transition hover:border-hb-blue">
                    <input type="checkbox" name="amenities[]" value="<?php echo e($amenity->id); ?>" class="h-4 w-4 rounded border-gray-300 text-hb-orange focus:ring-hb-orange"
                        <?php if(in_array($amenity->id, old('amenities', []))): echo 'checked'; endif; ?>>
                    <?php echo e($amenity->name); ?>

                </label>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <p class="col-span-full text-sm text-gray-400">Aucun équipement disponible.</p>
            <?php endif; ?>
        </div>
    </div>

    <div class="flex items-center gap-3">
<button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> Créer l'établissement</button>
        <a href="<?php echo e(route('hote.dashboard')); ?>" class="btn-outline"><i class="bi bi-x-lg"></i> Annuler</a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/backoffice/hote/create-establishment.blade.php ENDPATH**/ ?>