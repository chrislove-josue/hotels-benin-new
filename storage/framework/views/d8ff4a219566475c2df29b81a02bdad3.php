

<?php $__env->startSection('title', 'Contact — Hôtels Bénin'); ?>

<?php $__env->startSection('content'); ?>
<section class="relative overflow-hidden bg-gradient-to-br from-hb-blue to-hb-blue-deeper py-16 text-center text-white">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="relative mx-auto max-w-2xl px-4" data-reveal>
        <span class="badge-gold-solid mb-4">Une question ?</span>
        <h1 class="font-display text-4xl font-bold md:text-5xl">Contactez-nous</h1>
        <div class="mx-auto mt-4 h-1 w-20 rounded-full bg-gradient-to-r from-hb-gold to-hb-orange"></div>
        <p class="mt-5 text-white/85">Notre équipe vous répond sous 24h ouvrées.</p>
    </div>
</section>

<section class="mx-auto max-w-5xl px-4 py-16 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 gap-10 md:grid-cols-5" data-reveal>
        <div class="md:col-span-3">
            <div class="card p-6 sm:p-8">
                <?php if($errors->any()): ?>
                    <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">
                        <ul class="list-inside list-disc space-y-1">
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="<?php echo e(route('contact.store')); ?>" method="POST" class="space-y-5">
                    <?php echo csrf_field(); ?>
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-500">Nom complet</label>
                            <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="input-field" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-500">E-mail</label>
                            <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="input-field" required>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Sujet</label>
                        <input type="text" name="subject" value="<?php echo e(old('subject')); ?>" class="input-field">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Message</label>
                        <textarea name="message" rows="6" class="input-field" required><?php echo e(old('message')); ?></textarea>
                    </div>
<button type="submit" class="btn-primary w-full sm:w-auto"><i class="bi bi-send"></i> Envoyer le message</button>
                </form>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="card space-y-5 p-6 sm:p-8">
<?php $__currentLoopData = [
                    ['icon' => 'bi-geo-alt', 'label' => 'Adresse', 'value' => 'Cotonou, Bénin'],
                    ['icon' => 'bi-telephone', 'label' => 'Téléphone', 'value' => '+229 01 28 59 26 64', 'href' => 'tel:+2290128592664'],
                    ['icon' => 'bi-whatsapp', 'label' => 'WhatsApp', 'value' => '+229 01 28 59 26 64', 'href' => 'https://wa.me/2290128592664'],
                    ['icon' => 'bi-envelope', 'label' => 'E-mail', 'value' => 'hotelsbenin229@gmail.com', 'href' => 'mailto:hotelsbenin229@gmail.com'],
                    ['icon' => 'bi-clock', 'label' => 'Horaires', 'value' => 'Lun – Sam : 8h00 – 19h00'],
                ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $info): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-hb-gold/10 text-lg text-hb-orange"><i class="bi <?php echo e($info['icon']); ?>"></i></span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400"><?php echo e($info['label']); ?></p>
                            <?php if(!empty($info['href'])): ?>
                                <a href="<?php echo e($info['href']); ?>" <?php echo e(str_starts_with($info['href'], 'http') ? 'target=_blank rel=noopener noreferrer' : ''); ?> class="text-gray-700 transition hover:text-hb-orange"><?php echo e($info['value']); ?></a>
                            <?php else: ?>
                                <p class="text-gray-700"><?php echo e($info['value']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>
</section>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views\contact.blade.php ENDPATH**/ ?>