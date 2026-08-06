

<?php $__env->startSection('title', 'Modifications en attente — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <h1 class="font-display text-3xl font-bold text-hb-blue">Modifications en attente</h1>
    <p class="mt-1 text-sm text-gray-500">Approvez ou rejetez les modifications proposées par les hôtes.</p>
</div>


<div class="card mb-6 overflow-hidden">
    <div class="border-b border-gray-100 bg-hb-cream px-5 py-4">
        <h2 class="flex items-center gap-2 font-display text-lg font-bold text-hb-blue">
            <i class="bi bi-building"></i> Modifications d'établissements
            <span class="badge-pill bg-hb-orange/10 text-hb-orange-dark"><?php echo e($pendingEstablishments->count()); ?></span>
        </h2>
    </div>
    <div class="divide-y divide-gray-50">
        <?php $__empty_1 = true; $__currentLoopData = $pendingEstablishments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="font-semibold text-hb-blue"><?php echo e($change->establishment->name); ?></p>
                        <p class="mt-1 text-xs text-gray-500">
                            Proposé par <span class="font-semibold"><?php echo e($change->user->name ?? '—'); ?></span> · <?php echo e($change->created_at->diffForHumans()); ?>

                        </p>
                        <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-600">
                            <?php
                                $fields = [
                                    'name' => 'Nom',
                                    'establishment_type_id' => 'Type',
                                    'locality_id' => 'Localité',
                                    'address' => 'Adresse',
                                    'price_from' => 'Prix / nuit',
                                    'capacity' => 'Capacité',
                                    'short_description' => 'Courte description',
                                    'description' => 'Description',
                                    'cover_image' => 'Image',
                                    'amenities' => 'Équipements',
                                ];
                            ?>
                            <?php $__currentLoopData = $fields; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php if(array_key_exists($key, $change->data)): ?>
                                    <span class="badge-pill bg-hb-blue/10 text-hb-blue"><i class="bi bi-pencil-square mr-1"></i><?php echo e($label); ?></span>
                                <?php endif; ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <form action="<?php echo e(route('admin.pending.establishments.approve', $change->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-primary !px-3 !py-1.5 text-xs"><i class="bi bi-check-lg"></i> Approuver</button>
                        </form>
                        <form action="<?php echo e(route('admin.pending.establishments.reject', $change->id)); ?>" method="POST" onsubmit="return confirm('Rejeter cette modification ?');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-x-lg"></i> Rejeter</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="px-5 py-8 text-center text-sm text-gray-400">Aucune modification d'établissement en attente.</p>
        <?php endif; ?>
    </div>
</div>


<div class="card overflow-hidden">
    <div class="border-b border-gray-100 bg-hb-cream px-5 py-4">
        <h2 class="flex items-center gap-2 font-display text-lg font-bold text-hb-blue">
            <i class="bi bi-door-open"></i> Modifications de chambres
            <span class="badge-pill bg-hb-orange/10 text-hb-orange-dark"><?php echo e($pendingRooms->count()); ?></span>
        </h2>
    </div>
    <div class="divide-y divide-gray-50">
        <?php $__empty_1 = true; $__currentLoopData = $pendingRooms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $change): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <div class="p-5">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <p class="font-semibold text-hb-blue">
                            <span class="badge-pill mr-2 <?php echo e($change->action === 'create' ? 'bg-green-100 text-green-700' : ($change->action === 'delete' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700')); ?>">
                                <?php echo e($change->action === 'create' ? 'Création' : ($change->action === 'delete' ? 'Suppression' : 'Modification')); ?>

                            </span>
                            <?php echo e($change->room->name ?? ($change->data['name'] ?? 'Nouvelle chambre')); ?>

                        </p>
                        <p class="mt-1 text-xs text-gray-500">
                            Établissement : <span class="font-semibold"><?php echo e($change->establishment->name); ?></span> ·
                            Proposé par <span class="font-semibold"><?php echo e($change->user->name ?? '—'); ?></span> · <?php echo e($change->created_at->diffForHumans()); ?>

                        </p>
                        <?php if($change->data): ?>
                            <div class="mt-2 flex flex-wrap gap-3 text-xs text-gray-600">
                                <?php $__currentLoopData = $change->data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php if(is_string($value) || is_numeric($value)): ?>
                                        <span class="badge-pill bg-hb-blue/10 text-hb-blue"><i class="bi bi-pencil-square mr-1"></i><?php echo e($key); ?> : <?php echo e($value); ?></span>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="flex shrink-0 gap-2">
                        <form action="<?php echo e(route('admin.pending.rooms.approve', $change->id)); ?>" method="POST">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-primary !px-3 !py-1.5 text-xs"><i class="bi bi-check-lg"></i> Approuver</button>
                        </form>
                        <form action="<?php echo e(route('admin.pending.rooms.reject', $change->id)); ?>" method="POST" onsubmit="return confirm('Rejeter cette modification ?');">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="btn-danger !px-3 !py-1.5 text-xs"><i class="bi bi-x-lg"></i> Rejeter</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="px-5 py-8 text-center text-sm text-gray-400">Aucune modification de chambre en attente.</p>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\Projects\hotels-benin-new\resources\views\backoffice\admin\pending.blade.php ENDPATH**/ ?>