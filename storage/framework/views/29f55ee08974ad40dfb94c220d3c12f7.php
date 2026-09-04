

<?php $__env->startSection('title', 'Compte en attente — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="relative flex min-h-[75vh] items-center overflow-hidden bg-gradient-to-br from-hb-blue to-hb-blue-deeper px-4 py-16 sm:px-6">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-hb-orange/20 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-md" data-reveal>
        <div class="card w-full p-8 text-center">
            <span class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-hb-gold/20 text-hb-orange">
                <i class="bi bi-hourglass-split text-3xl"></i>
            </span>
            <h1 class="font-display text-2xl font-bold text-hb-blue-deeper">Compte en attente d'approbation</h1>
            <p class="mt-3 text-sm leading-relaxed text-gray-600">
                Votre compte a bien été créé. Un administrateur doit encore valider votre inscription
                avant que vous puissiez accéder à votre espace.
            </p>

            <div class="my-6 rounded-xl bg-hb-cream p-4 text-left text-sm text-gray-600">
                <ul class="space-y-2">
                    <li class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-green-500"></i> Compte enregistré
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="bi bi-clock-history text-hb-orange"></i> En attente de validation admin
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="bi bi-envelope text-hb-blue"></i> Vous serez contacté dès l'activation
                    </li>
                </ul>
            </div>

            <p class="text-xs text-gray-400">
                Une fois approuvé, vous pourrez vous connecter avec vos identifiants.
            </p>

            <div class="mt-6 flex flex-col gap-3">
                <a href="<?php echo e(route('home')); ?>" class="btn-primary w-full"><i class="bi bi-house"></i> Retour à l'accueil</a>
                <a href="<?php echo e(route('login.show')); ?>" class="btn-outline w-full">Aller à la connexion</a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views\auth\pending.blade.php ENDPATH**/ ?>