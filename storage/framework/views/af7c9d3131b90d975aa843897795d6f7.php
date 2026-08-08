<footer class="relative overflow-hidden bg-hb-blue-deeper text-white">
    
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/10 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-hb-blue-light/20 blur-3xl"></div>

    
    <div class="relative mx-auto max-w-7xl px-4 pt-16 sm:px-6 lg:px-8">
        <div class="glass flex flex-col items-center gap-6 px-6 py-10 text-center md:flex-row md:justify-between md:text-left">
            <div>
                <h3 class="font-display text-2xl font-bold">Restez informé des meilleures adresses</h3>
                <p class="mt-1 text-sm text-white/70">Recevez nos bons plans et nouveaux établissements du Bénin.</p>
            </div>
            <form class="flex w-full max-w-md gap-2" x-data="{ email: '', sent: false }" @submit.prevent="sent = true">
                <input x-model="email" type="email" required placeholder="Votre e-mail"
                       class="w-full rounded-full border border-white/20 bg-white/10 px-5 py-3 text-sm text-white placeholder-white/50 backdrop-blur focus:border-hb-gold focus:outline-none">
                <button type="submit" class="btn-gold shrink-0 !px-6">S'abonner</button>
            </form>
        </div>
    </div>

    
    <div class="relative mx-auto grid max-w-7xl grid-cols-1 gap-10 px-4 py-14 sm:px-6 md:grid-cols-2 lg:grid-cols-4 lg:px-8">
        <div>
            <div class="mb-4 flex items-center gap-2.5">
                <span class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-hb-gold to-hb-gold-light font-display text-lg font-bold text-hb-blue-deeper">HB</span>
                <span class="font-display text-xl font-bold">Hôtels Bénin</span>
            </div>
            <p class="text-sm leading-relaxed text-white/70">
                La plateforme qui rassemble hôtels, résidences, villas et salles de fête du Bénin, pour préparer votre séjour en toute confiance.
            </p>
<div class="mt-5 flex gap-3">
                <a href="#" aria-label="Facebook" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition hover:bg-hb-gold hover:text-hb-blue-deeper">
                    <i class="bi bi-facebook"></i>
                </a>
                <a href="#" aria-label="Instagram" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition hover:bg-hb-gold hover:text-hb-blue-deeper">
                    <i class="bi bi-instagram"></i>
                </a>
                <a href="#" aria-label="X" class="flex h-10 w-10 items-center justify-center rounded-full bg-white/10 transition hover:bg-hb-gold hover:text-hb-blue-deeper">
                    <i class="bi bi-twitter-x"></i>
                </a>
            </div>
        </div>

        <div>
            <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-hb-gold">Explorer</h3>
            <ul class="space-y-2.5 text-sm text-white/80">
                <li><a href="<?php echo e(route('establishments.index')); ?>" class="transition hover:text-hb-gold">Tous les établissements</a></li>
                <li><a href="<?php echo e(route('establishments.index', ['type' => 'hotel'])); ?>" class="transition hover:text-hb-gold">Hôtels</a></li>
                <li><a href="<?php echo e(route('establishments.index', ['type' => 'residence-meublee'])); ?>" class="transition hover:text-hb-gold">Résidences meublées</a></li>
                <li><a href="<?php echo e(route('establishments.index', ['type' => 'salle-de-fete'])); ?>" class="transition hover:text-hb-gold">Salles de fête</a></li>
                <li><a href="<?php echo e(route('events.index')); ?>" class="transition hover:text-hb-gold">Événements & programmes</a></li>
            </ul>
        </div>

        <div>
            <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-hb-gold">Plateforme</h3>
            <ul class="space-y-2.5 text-sm text-white/80">
                <li><a href="<?php echo e(route('about')); ?>" class="transition hover:text-hb-gold">À propos</a></li>
                <li><a href="<?php echo e(route('contact.create')); ?>" class="transition hover:text-hb-gold">Contact</a></li>
                <li><a href="<?php echo e(route('login.show')); ?>" class="transition hover:text-hb-gold">Connexion</a></li>
                <li><a href="<?php echo e(route('register.show')); ?>" class="transition hover:text-hb-gold">Devenir hôte</a></li>
            </ul>
        </div>

        <div>
            <h3 class="mb-4 text-sm font-bold uppercase tracking-wider text-hb-gold">Contact</h3>
<ul class="space-y-2.5 text-sm text-white/80">
                <li class="flex items-start gap-2"><i class="bi bi-geo-alt mt-0.5 text-hb-gold"></i> Cotonou, Bénin</li>
                <li class="flex items-center gap-2"><i class="bi bi-telephone text-hb-gold"></i> +229 21 30 00 00</li>
                <li class="flex items-center gap-2"><i class="bi bi-envelope text-hb-gold"></i> contact@hotels-benin.bj</li>
                <li class="flex items-start gap-2"><i class="bi bi-clock mt-0.5 text-hb-gold"></i> Lun – Sam : 8h00 – 19h00</li>
            </ul>
        </div>
    </div>

    <div class="relative border-t border-white/10 py-6 text-center text-xs text-white/60">
        &copy; <?php echo e(date('Y')); ?> Hôtels Bénin — Tous droits réservés. Fait avec <span class="text-hb-orange">♥</span> au Bénin.
    </div>
</footer>
<?php /**PATH E:\Projects\hotels-benin-new\resources\views\partials\footer.blade.php ENDPATH**/ ?>