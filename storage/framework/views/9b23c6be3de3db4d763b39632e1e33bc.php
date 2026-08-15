

<?php $__env->startSection('title', 'Messages — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Messages de contact</h1>
    <p class="mt-1 text-sm text-gray-500">Messages reçus via le formulaire de contact.</p>
</div>

<div class="space-y-4">
    <?php $__empty_1 = true; $__currentLoopData = $messages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card p-5 <?php echo e(!$message->is_read ? 'border-l-4 border-hb-orange' : ''); ?>">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <p class="font-semibold text-hb-blue"><?php echo e($message->name); ?></p>
                        <span class="text-xs text-gray-400">· <?php echo e($message->email); ?></span>
                        <?php if(!$message->is_read): ?>
                            <span class="badge-pill bg-hb-orange/10 text-hb-orange-dark">Non lu</span>
                        <?php endif; ?>
                    </div>
                    <p class="mt-1 text-xs text-gray-400"><?php echo e($message->created_at->diffForHumans()); ?></p>
                </div>
                <?php if(!$message->is_read): ?>
                    <form action="<?php echo e(route('admin.messages.read', $message->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?>
<button type="submit" class="btn-outline !px-3 !py-1.5 text-xs"><i class="bi bi-envelope-open"></i> Marquer comme lu</button>
                    </form>
                <?php endif; ?>
            </div>

            <?php if($message->subject): ?>
                <p class="mt-3 text-sm font-semibold text-gray-700"><?php echo e($message->subject); ?></p>
            <?php endif; ?>
            <p class="mt-1 text-sm leading-relaxed text-gray-600"><?php echo e($message->message); ?></p>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="card p-10 text-center text-gray-400">Aucun message.</div>
    <?php endif; ?>
</div>

<div class="mt-6">
    <?php echo e($messages->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views/backoffice/admin/messages.blade.php ENDPATH**/ ?>