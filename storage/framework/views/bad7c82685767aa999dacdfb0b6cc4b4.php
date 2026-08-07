

<?php $__env->startSection('title', isset($event) ? 'Modifier l\'événement — Administration' : 'Nouvel événement — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <a href="<?php echo e(route('admin.events')); ?>" class="mb-2 inline-flex items-center gap-1 text-xs font-semibold text-hb-orange hover:underline">
        <i class="bi bi-arrow-left"></i> Retour aux événements
    </a>
    <h1 class="font-display text-3xl font-bold text-hb-blue"><?php echo e(isset($event) ? "Modifier : {$event->title}" : 'Nouvel événement'); ?></h1>
    <p class="mt-1 text-sm text-gray-500"><?php echo e(isset($event) ? 'Modification appliquée immédiatement sur la vitrine publique.' : 'Créez un événement, festival ou programme du Bénin.'); ?></p>
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

<form action="<?php echo e(isset($event) ? route('admin.events.update', $event->id) : route('admin.events.store')); ?>" method="POST" enctype="multipart/form-data" class="card p-6 space-y-4">
    <?php echo csrf_field(); ?>
    <?php if(isset($event)): ?>
        <?php echo method_field('PUT'); ?>
    <?php endif; ?>

    <div>
        <label class="label">Titre *</label>
        <input type="text" name="title" value="<?php echo e(old('title', $event->title ?? '')); ?>" class="input-field" required>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="label">Catégorie *</label>
            <select name="category" class="input-field" required>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($value); ?>" <?php if(old('category', $event->category ?? '') === $value): echo 'selected'; endif; ?>><?php echo e($label); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div>
            <label class="label">Lieu</label>
            <input type="text" name="location" value="<?php echo e(old('location', $event->location ?? '')); ?>" class="input-field">
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div>
            <label class="label">Date de début *</label>
            <input type="date" name="date_start" value="<?php echo e(old('date_start', isset($event) && $event->date_start ? $event->date_start->format('Y-m-d') : '')); ?>" class="input-field" required>
        </div>
        <div>
            <label class="label">Date de fin</label>
            <input type="date" name="date_end" value="<?php echo e(old('date_end', isset($event) && $event->date_end ? $event->date_end->format('Y-m-d') : '')); ?>" class="input-field">
        </div>
    </div>

<div>
        <label class="label">Image de couverture (URL)</label>
        <input type="url" name="cover_image" value="<?php echo e(old('cover_image', $event->cover_image ?? '')); ?>" class="input-field" placeholder="https://...">
        <?php if(isset($event) && $event->cover_image): ?>
            <img src="<?php echo e($event->cover_image); ?>" alt="Aperçu" class="mt-2 h-24 w-40 rounded-lg object-cover">
        <?php endif; ?>
    </div>

    <div>
        <label class="label">Ou téléverser une image</label>
        <input type="file" name="cover_image_file" accept="image/*" class="input-field">
        <p class="mt-1 text-xs text-gray-400">JPG, PNG, WebP — 5 Mo max. Prioritaire sur l'URL.</p>
    </div>

    <div>
        <label class="label">Courte description</label>
        <input type="text" name="short_description" value="<?php echo e(old('short_description', $event->short_description ?? '')); ?>" class="input-field">
    </div>

    <div>
        <label class="label">Description *</label>
        <textarea name="description" rows="6" class="input-field" required><?php echo e(old('description', $event->description ?? '')); ?></textarea>
    </div>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
            <input type="checkbox" name="is_available" value="1" class="h-4 w-4 rounded text-hb-orange focus:ring-hb-orange" <?php if(old('is_available', $event->is_available ?? true)): echo 'checked'; endif; ?>>
            Visible sur la vitrine publique
        </label>
        <label class="flex cursor-pointer items-center gap-2 rounded-xl border border-gray-200 px-4 py-3 text-sm text-gray-700">
            <input type="checkbox" name="is_featured" value="1" class="h-4 w-4 rounded text-hb-gold focus:ring-hb-gold" <?php if(old('is_featured', $event->is_featured ?? false)): echo 'checked'; endif; ?>>
            <i class="bi bi-star-fill text-hb-gold"></i> À la une
        </label>
    </div>

    <div class="flex items-center gap-3">
        <button type="submit" class="btn-primary"><i class="bi bi-check-lg"></i> <?php echo e(isset($event) ? 'Enregistrer' : 'Créer l\'événement'); ?></button>
        <a href="<?php echo e(route('admin.events')); ?>" class="btn-outline">Annuler</a>
    </div>
</form>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/admin/edit-event.blade.php ENDPATH**/ ?>