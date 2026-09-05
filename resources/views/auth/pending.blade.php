@extends('layouts.app')

@section('title', 'Compte en attente — Hôtels Bénin')

@section('content')
<section class="relative flex min-h-[75vh] items-center overflow-hidden bg-gradient-to-br from-hb-blue to-hb-blue-deeper px-4 py-16 sm:px-6">
    <div class="pointer-events-none absolute -right-24 top-0 h-72 w-72 rounded-full bg-hb-gold/20 blur-3xl"></div>
    <div class="pointer-events-none absolute -left-24 bottom-0 h-72 w-72 rounded-full bg-hb-orange/20 blur-3xl"></div>

    <div class="relative mx-auto w-full max-w-md" data-reveal>
        <div class="card w-full p-8 text-center">
            <span class="mx-auto mb-5 flex h-16 w-16 items-center justify-center rounded-full bg-hb-gold/20 text-hb-orange">
                <i class="bi bi-envelope-check text-3xl"></i>
            </span>
            <h1 class="font-display text-2xl font-bold text-hb-blue-deeper">Vérifiez votre boîte e-mail</h1>
            <p class="mt-3 text-sm leading-relaxed text-gray-600">
                Votre compte a bien été créé. Un e-mail vient de vous être envoyé avec vos
                identifiants de connexion et un lien sécurisé pour <strong>valider votre compte</strong>
                et accéder à votre espace.
            </p>

            <div class="my-6 rounded-xl bg-hb-cream p-4 text-left text-sm text-gray-600">
                <ul class="space-y-2">
                    <li class="flex items-center gap-2">
                        <i class="bi bi-check-circle-fill text-green-500"></i> Compte enregistré
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="bi bi-envelope text-hb-blue"></i> E-mail avec vos identifiants envoyé
                    </li>
                    <li class="flex items-center gap-2">
                        <i class="bi bi-link-45deg text-hb-orange"></i> Cliquez sur le lien pour vous connecter et valider votre compte
                    </li>
                </ul>
            </div>

            <p class="text-xs text-gray-400">
                Le lien est valable 24 heures et ne peut être utilisé qu'une seule fois.
                Pensez à vérifier vos spams.
            </p>

            <div class="mt-6 flex flex-col gap-3">
                <a href="{{ route('home') }}" class="btn-primary w-full"><i class="bi bi-house"></i> Retour à l'accueil</a>
                <a href="{{ route('login.show') }}" class="btn-outline w-full">Aller à la connexion</a>
            </div>
        </div>
    </div>
</section>
@endsection

