<?php $__env->startSection('title', 'Paramètres de paiement — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <nav class="mb-4 text-sm text-gray-500">
        <span class="font-semibold text-hb-blue">Paramètres de paiement</span>
    </nav>
    <h1 class="font-display text-3xl font-bold text-hb-blue">Agrégateurs de paiement</h1>
    <p class="mt-1 text-sm text-gray-500">Activez FedaPay et/ou KKiaPay et choisissez celui utilisé au paiement. Le voyageur passe toujours par l'agrégateur par défaut.</p>
</div>

<div class="card mx-auto max-w-3xl p-6 sm:p-8">
    <form action="<?php echo e(route('admin.payments.settings.update')); ?>" method="POST" class="space-y-6">
        <?php echo csrf_field(); ?>

        <?php if($errors->any()): ?>
            <div class="rounded-xl bg-red-50 p-3 text-xs text-red-600">
                <ul class="space-y-1">
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li>• <?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        
        <div>
            <span class="mb-2 block text-xs font-semibold text-gray-500">Agrégateurs actifs</span>
            <div class="space-y-3">
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-100 bg-hb-cream p-4">
                    <input type="checkbox" name="providers[]" value="fedapay" class="h-5 w-5 accent-hb-blue" <?php if(in_array('fedapay', $enabled, true)): echo 'checked'; endif; ?>>
                    <span class="text-sm text-gray-700">
                        <span class="font-semibold text-hb-blue">FedaPay</span>
                        <span class="block text-xs text-gray-500">
                            Environnement : <strong><?php echo e(strtoupper($fedapay['environment'] ?? 'sandbox')); ?></strong>
                            <?php if(!$fedapay['public_key']): ?> · <span class="text-red-500">clé publique absente (.env)</span><?php endif; ?>
                        </span>
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-100 bg-hb-cream p-4">
                    <input type="checkbox" name="providers[]" value="kkiapay" class="h-5 w-5 accent-hb-blue" <?php if(in_array('kkiapay', $enabled, true)): echo 'checked'; endif; ?>>
                    <span class="text-sm text-gray-700">
                        <span class="font-semibold text-hb-blue">KKiaPay</span>
                        <span class="block text-xs text-gray-500">Paiement par mobile money et carte bancaire.</span>
                    </span>
                </label>
            </div>
        </div>

        
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Agrégateur utilisé par défaut</label>
            <select name="default_provider" class="input-field" required>
                <option value="fedapay" <?php if($default === 'fedapay'): echo 'selected'; endif; ?>>FedaPay</option>
                <option value="kkiapay" <?php if($default === 'kkiapay'): echo 'selected'; endif; ?>>KKiaPay</option>
            </select>
            <p class="mt-1 text-xs text-gray-400">Doit correspondre à un agrégateur activé ci-dessus.</p>
        </div>

        
        <div class="rounded-xl border border-gray-100 bg-hb-cream p-4">
            <span class="mb-3 block text-xs font-semibold text-gray-500">Clés API KKiaPay <span class="font-normal">(laisser vide pour conserver)</span></span>

            <div class="space-y-3">
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Clé publique</label>
                    <input type="text" name="kkiapay_public_key" value="<?php echo e(old('kkiapay_public_key')); ?>" class="input-field" placeholder="<?php echo e($kkiapay['public_key'] ? '•••• (déjà configurée)' : 'Clé publique KKiaPay'); ?>">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Clé privée</label>
                    <input type="text" name="kkiapay_private_key" value="<?php echo e(old('kkiapay_private_key')); ?>" class="input-field" placeholder="<?php echo e($kkiapay['private_key'] ? '•••• (déjà configurée)' : 'Clé privée KKiaPay'); ?>">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Secrète</label>
                    <input type="text" name="kkiapay_secret" value="<?php echo e(old('kkiapay_secret')); ?>" class="input-field" placeholder="<?php echo e($kkiapay['secret'] ? '•••• (déjà configurée)' : 'Secrète KKiaPay'); ?>">
                </div>
                <div>
                    <label class="mb-1 block text-xs font-medium text-gray-500">Environnement</label>
                    <select name="kkiapay_environment" class="input-field">
                        <option value="sandbox" <?php if(($kkiapay['environment'] ?? 'sandbox') === 'sandbox'): echo 'selected'; endif; ?>>Sandbox (test)</option>
                        <option value="production" <?php if(($kkiapay['environment'] ?? 'sandbox') === 'production'): echo 'selected'; endif; ?>>Production</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row">
            <button type="submit" class="btn-primary flex-1"><i class="bi bi-check-lg"></i> Enregistrer</button>
            <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn-outline flex-1"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/backoffice/admin/payment-settings.blade.php ENDPATH**/ ?>