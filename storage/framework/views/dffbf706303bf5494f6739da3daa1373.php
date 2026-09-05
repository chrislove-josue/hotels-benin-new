
<div class="flex flex-wrap items-center justify-end gap-1.5">
    <?php if (! ($user->isAdmin())): ?>
        <a href="<?php echo e(route('admin.users.impersonate', $user->id)); ?>"
           class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-purple-100 text-purple-700 transition hover:bg-purple-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-purple-500 focus-visible:ring-offset-1"
           title="Ouvrir sa session (impersonation)"
           onclick="return confirm('Ouvrir la session de <?php echo e($user->name); ?> ? Vous pourrez revenir à l\'espace admin à tout moment via le bandeau orange.');">
            <i class="bi bi-box-arrow-in-right text-base"></i>
        </a>
    <?php endif; ?>

    <?php if(! $user->is_approved): ?>
        <form action="<?php echo e(route('admin.users.approve', $user->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-green-100 text-green-700 transition hover:bg-green-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-green-500 focus-visible:ring-offset-1" title="Approuver">
                <i class="bi bi-check-circle text-base"></i>
            </button>
        </form>
    <?php else: ?>
        <form action="<?php echo e(route('admin.users.reject', $user->id)); ?>" method="POST">
            <?php echo csrf_field(); ?>
            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-orange-100 text-orange-700 transition hover:bg-orange-500 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-orange-400 focus-visible:ring-offset-1" title="Désactiver">
                <i class="bi bi-pause-circle text-base"></i>
            </button>
        </form>
    <?php endif; ?>

    <a href="<?php echo e(route('admin.users.edit', $user->id)); ?>" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-hb-blue/10 text-hb-blue transition hover:bg-hb-blue hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-hb-blue focus-visible:ring-offset-1" title="Modifier">
        <i class="bi bi-pencil text-base"></i>
    </a>

    <?php if($user->id !== auth()->id()): ?>
        <form action="<?php echo e(route('admin.users.destroy', $user->id)); ?>" method="POST"
              onsubmit="return confirm('Supprimer définitivement cet utilisateur ?');">
            <?php echo csrf_field(); ?>
            <?php echo method_field('DELETE'); ?>
            <button type="submit" class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-red-100 text-red-600 transition hover:bg-red-600 hover:text-white focus:outline-none focus-visible:ring-2 focus-visible:ring-red-500 focus-visible:ring-offset-1" title="Supprimer">
                <i class="bi bi-trash text-base"></i>
            </button>
        </form>
    <?php endif; ?>
</div>
<?php /**PATH C:\Project\hotels-benin-new\resources\views/backoffice/admin/partials/user-actions.blade.php ENDPATH**/ ?>