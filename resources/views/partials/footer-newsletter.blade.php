    {{-- CTA newsletter --}}
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