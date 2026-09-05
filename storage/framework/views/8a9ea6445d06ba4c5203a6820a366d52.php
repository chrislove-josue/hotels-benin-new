<?php $__env->startSection('title', 'Compte en attente — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="relative flex min-h-[75vh] items-center overflow-hidden bg-gradient-to-br from-hb-blue to-hb-blue-deeper px-4 py-16 sm:px-6">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-hb-orange/20 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-md" data-reveal>
        <div class="card w-full p-8 text-center">
            <span class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-hb-gold/20 text-hb-orange">
                <i class="bi bi-envelope-check text-3xl"></i>
            </span>
            <h1 class="font-display text-2xl font-bold text-hb-blue-deeper">Vérifiez votre boîte e-mail</h1>

            <?php if(session('mail_sent') === false): ?>
                <p class="mt-3 text-sm leading-relaxed text-gray-600">
                    Votre compte a bien été créé. L'e-mail d'accueil avec vos identifiants
                    et le lien sécurisé n'a pas pu être envoyé : le serveur de messagerie l'a refusé.
                </p>
            <?php else: ?>
                <p class="mt-3 text-sm leading-relaxed text-gray-600">
                    Votre compte a bien été créé. Un e-mail vous a été envoyé avec vos
                    identifiants de connexion et un lien sécurisé pour <strong>valider votre compte</strong>
                    et accéder à votre espace.
                </p>
            <?php endif; ?>

            <?php if(session('mail_sent') === false): ?>
                <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-left text-sm text-red-700">
                    <p class="font-semibold flex items-center gap-2"><i class="bi bi-exclamation-triangle-fill"></i> L'e-mail n'a pas pu être envoyé</p>
                    <p class="mt-1 leading-relaxed">
                        Contactez l'administration de l'Hôtels Bénin via
                        <a href="https://wa.me/2290128592664" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-semibold text-green-600 underline decoration-green-300 underline-offset-2 hover:text-green-700"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                        pour une prise en charge.
                    </p>
                </div>
            <?php endif; ?>

            <div class="my-6 rounded-xl bg-hb-cream p-4 text-left text-sm text-gray-600">
                <ul class="space-y-2">
                    <li class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-green-500"></i> Compte enregistré
                    </li>
                    <li class="flex items-center gap-2">
                        <?php if(session('mail_sent') === false): ?>
                            <i class="bi bi-x-circle-fill text-red-500"></i> E-mail avec vos identifiants <strong class="text-red-600">non envoyé</strong>
                        <?php else: ?>
                            <i class="bi bi-envelope text-hb-blue"></i> E-mail avec vos identifiants envoyé
                        <?php endif; ?>
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="bi bi-link-45deg text-hb-orange"></i> Cliquez sur le lien pour vous connecter et valider votre compte
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="bi bi-clock-history text-gray-400"></i> Le lien est valable 24 heures et ne peut être utilisé qu'une seule fois
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="bi bi-whatsapp text-green-600"></i> Besoin d'aide ? Contactez l'administration via
                        <a href="https://wa.me/2290128592664" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1 font-semibold text-green-600 underline decoration-green-300 underline-offset-2 hover:text-green-700"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                    </li>
                </ul>
            </div>

            <p class="text-xs text-gray-400">
                Pensez à vérifier vos spams si l'e-mail n'apparaît pas rapidement.
            </p>

            <div class="mt-6 flex flex-col gap-3">
                <a href="<?php echo e(route('home')); ?>" class="btn-primary w-full"><i class="bi bi-house"></i> Retour à l'accueil</a>
                <a href="<?php echo e(route('login.show')); ?>" class="btn-outline text-green-600 w-full">Aller à la connexion</a>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/auth/pending.blade.php ENDPATH**/ ?>