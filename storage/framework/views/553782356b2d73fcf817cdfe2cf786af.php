

<?php $__env->startSection('title', 'Mot de passe oublié — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="relative flex min-h-[75vh] items-center overflow-hidden bg-gradient-to-br from-hb-blue to-hb-blue-deeper px-4 py-16 sm:px-6">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-hb-orange/20 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-md" data-reveal>
        <div class="card w-full p-8">
            <div class="mb-6 text-center">
                <span class="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-gradient-to-br from-hb-blue to-hb-blue-deeper font-display text-xl font-bold text-hb-gold shadow-md"><i class="bi bi-key"></i></span>
                <h1 class="font-display text-2xl font-bold text-hb-blue-deeper">Mot de passe oublié</h1>
                <p class="mt-1 text-sm text-gray-500">Indiquez votre adresse e-mail : nous vous enverrons un lien pour réinitialiser votre mot de passe.</p>
            </div>

            <?php if(session('status')): ?>
                <div class="mb-6 rounded-xl bg-emerald-50 p-4 text-sm text-emerald-700">
                    <?php echo e(session('status')); ?>

                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-inside list-disc space-y-1">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('password.email')); ?>" method="POST" class="space-y-5">
                <?php echo csrf_field(); ?>
                <div>
                    <label for="email" class="mb-1 block text-xs font-semibold text-gray-500">E-mail</label>
                    <input id="email" type="email" name="email" value="<?php echo e(old('email')); ?>" class="input-field" required autofocus>
                </div>
                <button type="submit" class="btn-primary w-full"><i class="bi bi-envelope-paper"></i> Envoyer le lien de réinitialisation</button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Vous vous souvenez de votre mot de passe ?
                <a href="<?php echo e(route('login.show')); ?>" class="font-semibold text-hb-blue">Se connecter</a>
            </p>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/auth/forgot-password.blade.php ENDPATH**/ ?>