<?php $__env->startSection('title', 'Paiement — '.$reservation->reference.' — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <nav class="mb-6 flex items-center gap-1 text-sm text-gray-500">
        <a href="<?php echo e(route('home')); ?>" class="hover:text-hb-blue">Accueil</a> <span>/</span>
        <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="hover:text-hb-blue"><?php echo e($reservation->reference); ?></a> <span>/</span>
        <span class="font-semibold text-hb-blue">Paiement</span>
    </nav>

    <div class="mb-6 text-center">
        <h1 class="font-display text-3xl font-bold text-hb-blue">Paiement <?php echo e($sandbox ? 'de test' : ''); ?></h1>
        <p class="mt-1 text-sm text-gray-500">
            Réservation <span class="font-mono font-semibold text-hb-orange"><?php echo e($reservation->reference); ?></span> ·
            <?php echo e($reservation->establishment->name); ?>

        </p>
        <p class="mt-3 font-display text-2xl font-bold text-hb-blue"><?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA</p>
    </div>

    <div class="card p-6 text-center">
        <p class="mb-6 text-sm text-gray-600">
            <i class="bi bi-credit-card"></i>
            La fenêtre de paiement KKiaPay va s'ouvrir. Effectuez le règlement par mobile money ou carte bancaire.
        </p>

        
        <button type="button" id="kkiapay-pay"
            class="btn-primary w-full">
            <i class="bi bi-wallet2"></i> Ouvrir le paiement — <?php echo e(number_format($reservation->amount, 0, ',', ' ')); ?> FCFA
        </button>

        <div id="kkiapay-spinner" class="mt-4 hidden items-center justify-center gap-2 text-sm text-gray-500">
            <span class="h-4 w-4 animate-spin rounded-full border-2 border-hb-blue border-t-transparent"></span>
            En attente du paiement…
        </div>

        <a href="<?php echo e(route('reservations.show', $reservation)); ?>" class="mt-6 inline-block text-center text-sm font-semibold text-hb-blue hover:underline">
            <i class="bi bi-arrow-left"></i> Annuler et revenir à la réservation
        </a>
    </div>
</section>

<script src="https://cdn.kkiapay.me/k.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const callbackUrl = <?php echo json_encode($callbackUrl, 15, 512) ?>;
        const stateData = { reservation_id: <?php echo e($reservation->id); ?> };

        const openWidget = function () {
            openKkiapayWidget({
                amount: <?php echo json_encode(number_format((float) $reservation->amount, 0, '.') ?>,
                key: <?php echo json_encode($publicKey, 15, 512) ?>,
                sandbox: <?php echo e($sandbox ? 'true' : 'false'); ?>,
                data: JSON.stringify(stateData),
                name: <?php echo json_encode($reservation->user?->name ?? 'Client', 15, 512) ?>,
                email: <?php echo json_encode($reservation->user?->email ?? '', 15, 512) ?>,
                callback: callbackUrl,
                theme: '#1A3A6B',
                position: 'center',
                countries: ['BJ'],
                paymentmethod: 'momo,card'
            });

            document.getElementById('kkiapay-spinner')?.classList.remove('hidden');
            document.getElementById('kkiapay-spinner')?.classList.add('flex');
        };

        // Redirection vers le callback avec l'identifiant de transaction.
        addSuccessListener(function (response) {
            const transactionId = response && (response.transactionId || response.id);
            let url = callbackUrl;
            if (transactionId) {
                url += (url.includes('?') ? '&' : '?') + 'transactionId=' + encodeURIComponent(transactionId);
            }
            window.location.href = url;
        });

        document.getElementById('kkiapay-pay').addEventListener('click', openWidget);

        // Ouvre automatiquement le widget dès le chargement de la page.
        setTimeout(openWidget, 400);
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views/reservations/payment-kkiapay.blade.php ENDPATH**/ ?>