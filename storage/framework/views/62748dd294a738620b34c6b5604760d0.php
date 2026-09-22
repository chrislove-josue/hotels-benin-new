

<?php $__env->startSection('title', 'Nouvel utilisateur — Administration'); ?>

<?php $__env->startSection('content'); ?>
<div class="mb-6">
    <nav class="mb-4 text-sm text-gray-500">
        <a href="<?php echo e(route('admin.users')); ?>" class="hover:text-hb-orange">Utilisateurs</a> <span class="mx-1">/</span>
        <span class="font-semibold text-hb-blue">Nouvel utilisateur</span>
    </nav>
    <h1 class="font-display text-3xl font-bold text-hb-blue">Créer un utilisateur</h1>
    <p class="mt-1 text-sm text-gray-500">Ajoutez manuellement un compte d'hôte, de voyageur ou d'administrateur.</p>
</div>

<div class="card mx-auto max-w-2xl p-6 sm:p-8">
    <form action="<?php echo e(route('admin.users.store')); ?>" method="POST" class="space-y-4">
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
            <label class="mb-1 block text-xs font-semibold text-gray-500">Nom complet</label>
            <input type="text" name="name" value="<?php echo e(old('name')); ?>" class="input-field" required placeholder="Ex : Jean Kouassi">
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Adresse e-mail</label>
            <input type="email" name="email" value="<?php echo e(old('email')); ?>" class="input-field" required placeholder="jean@exemple.com">
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Téléphone / WhatsApp</label>
            <input type="text" name="phone" value="<?php echo e(old('phone')); ?>" class="input-field" placeholder="+229 01 00 00 00 00">
        </div>

        <div class="rounded-xl border border-hb-gold/30 bg-hb-cream p-4 text-sm text-gray-700">
            <div class="flex items-start gap-3">
                <i class="bi bi-key mt-0.5 text-hb-orange"></i>
                <div>
                    <p class="font-semibold text-hb-blue">Mot de passe généré automatiquement</p>
                    <p class="mt-1 text-xs text-gray-500">
                        Le mot de passe sera créé automatiquement et envoyé par e-mail à l'utilisateur
                        avec un lien sécurisé pour valider son compte et se connecter directement.
                    </p>
                </div>
            </div>
        </div>

        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Profil / Rôle</label>
            <select name="role" class="input-field" required>
                <option value="voyageur" <?php if(old('role') === 'voyageur'): echo 'selected'; endif; ?>>Voyageur</option>
                <option value="hote" <?php if(old('role') === 'hote'): echo 'selected'; endif; ?>>Hôte</option>
                <option value="admin" <?php if(old('role') === 'admin'): echo 'selected'; endif; ?>>Administrateur</option>
            </select>
        </div>

        <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-100 bg-hb-cream p-4">
            <input type="checkbox" name="is_approved" value="1" class="h-5 w-5 accent-hb-blue" checked>
            <span class="text-sm text-gray-700">
                <span class="font-semibold text-hb-blue">Compte approuvé</span>
                <span class="block text-xs text-gray-500">L'utilisateur pourra se connecter immédiatement.</span>
            </span>
        </label>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row">
            <button type="submit" class="btn-primary flex-1"><i class="bi bi-person-plus"></i> Créer l'utilisateur</button>
            <a href="<?php echo e(route('admin.users')); ?>" class="btn-outline flex-1"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.backoffice', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Project\hotels-benin-new\resources\views\backoffice\admin\create-user.blade.php ENDPATH**/ ?>