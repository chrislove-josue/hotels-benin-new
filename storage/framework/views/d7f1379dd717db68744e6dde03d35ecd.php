

<?php $__env->startSection('title', 'Inscription — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="relative flex min-h-[75vh] items-center overflow-hidden bg-gradient-to-br from-hb-blue to-hb-blue-deeper px-4 py-16 sm:px-6">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-hb-orange/20 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-md" data-reveal>
        <div class="card w-full p-8">
            <div class="mb-6 text-center">
                <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-hb-blue to-hb-blue-deeper font-display text-xl font-bold text-hb-gold shadow-md">HB</span>
                <h1 class="font-display text-2xl font-bold text-hb-blue-deeper">Créer un compte</h1>
                <p class="mt-1 text-sm text-gray-500">Rejoignez la communauté Hôtels Bénin.</p>
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

            <form action="<?php echo e(route('register.submit')); ?>" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Nom complet</label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="input-field" required>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">E-mail</label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="input-field" required>
                </div>
<div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Téléphone / WhatsApp <span class="text-hb-orange">*</span></label>
                    <div class="relative">
                        <i class="bi bi-whatsapp pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 text-hb-gold"></i>
                        <input type="tel" name="phone" value="<?php echo e(old('phone')); ?>" class="input-field !pl-10" placeholder="+229 01 23 45 67" required>
                    </div>
                    <p class="mt-1 text-xs text-gray-400">Utilisé pour vous contacter et pour WhatsApp.</p>
                </div>
                <div>
                    <label class="mb-1 block text-xs font-semibold text-gray-500">Vous êtes</label>
                    <select name="role" class="input-field">
                        <option value="voyageur">Voyageur — je cherche un hébergement</option>
                        <option value="hote">Hôte — je propose un établissement</option>
                    </select>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Mot de passe</label>
                        <input type="password" name="password" class="input-field" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Confirmation</label>
                        <input type="password" name="password_confirmation" class="input-field" required>
                    </div>
                </div>
<button type="submit" class="btn-primary w-full"><i class="bi bi-person-plus"></i> Créer mon compte</button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Déjà inscrit ?
                <a href="<?php echo e(route('login.show')); ?>" class="font-semibold text-hb-blue">Se connecter</a>
            </p>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/auth/register.blade.php ENDPATH**/ ?>