
<div x-data="{ addNew: <?php echo e($addNewInit ?? 'false'); ?> }">
    <label class="label">Localité *</label>

    <template x-if="!addNew">
        <div>
            <select name="locality_id" class="input-field" required>
                <option value="">Sélectionner...</option>
                <?php $__currentLoopData = $localities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $locality): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($locality->id); ?>" <?php if(($selected ?? null) == $locality->id): ?> selected <?php endif; ?>><?php echo e($locality->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="button" @click="addNew = true"
                class="mt-1.5 inline-flex items-center gap-1 text-xs font-semibold text-hb-blue transition hover:text-hb-orange">
                <i class="bi bi-plus-circle"></i> Votre ville n'existe pas ? Ajoutez-la
            </button>
        </div>
    </template>

    <template x-if="addNew">
        <div>
            <input type="text" name="new_locality" class="input-field" placeholder="Nom de la ville (ex : Abomey-Calavi)"
                value="<?php echo e($newLocalityValue ?? ''); ?>" maxlength="100">
            <button type="button" @click="addNew = false"
                class="mt-1.5 inline-flex items-center gap-1 text-xs font-semibold text-gray-500 transition hover:text-hb-orange">
                <i class="bi bi-arrow-left-circle"></i> Choisir une localité existante
            </button>
        </div>
    </template>

    <?php $__errorArgs = ['locality_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
    <?php $__errorArgs = ['new_locality'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
        <p class="mt-1 text-xs text-red-600"><?php echo e($message); ?></p>
    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
</div><?php /**PATH C:\Project\hotels-benin-new\resources\views/backoffice/partials/locality-picker.blade.php ENDPATH**/ ?>