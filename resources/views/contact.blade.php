@extends('layouts.app')

@section('title', 'Contact — Hôtels Bénin')

@section('content')
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
                @if ($errors->any())
                    <div class="mb-6 rounded-xl bg-red-50 p-4 text-sm text-red-700">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('contact.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-500">Nom complet</label>
                            <input type="text" name="name" value="{{ old('name') }}" class="input-field" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold text-gray-500">E-mail</label>
                            <input type="email" name="email" value="{{ old('email') }}" class="input-field" required>
                        </div>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Sujet</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="input-field">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold text-gray-500">Message</label>
                        <textarea name="message" rows="6" class="input-field" required>{{ old('message') }}</textarea>
                    </div>
<button type="submit" class="btn-primary w-full sm:w-auto"><i class="bi bi-send"></i> Envoyer le message</button>
                </form>
            </div>
        </div>

        <div class="md:col-span-2">
            <div class="card space-y-5 p-6 sm:p-8">
@foreach([
                    ['icon' => 'bi-geo-alt', 'label' => 'Adresse', 'value' => 'Cotonou, Bénin'],
                    ['icon' => 'bi-telephone', 'label' => 'Téléphone', 'value' => '+229 01 28 59 26 64', 'href' => 'tel:+2290128592664'],
                    ['icon' => 'bi-whatsapp', 'label' => 'WhatsApp', 'value' => '+229 01 28 59 26 64', 'href' => 'https://wa.me/2290128592664'],
                    ['icon' => 'bi-envelope', 'label' => 'E-mail', 'value' => 'hotelsbenin229@gmail.com', 'href' => 'mailto:hotelsbenin229@gmail.com'],
                    ['icon' => 'bi-clock', 'label' => 'Horaires', 'value' => 'Lun – Sam : 8h00 – 19h00'],
                ] as $info)
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-hb-gold/10 text-lg text-hb-orange"><i class="bi {{ $info['icon'] }}"></i></span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-400">{{ $info['label'] }}</p>
                            @if (!empty($info['href']))
                                <a href="{{ $info['href'] }}" {{ str_starts_with($info['href'], 'http') ? 'target=_blank rel=noopener noreferrer' : '' }} class="text-gray-700 transition hover:text-hb-orange">{{ $info['value'] }}</a>
                            @else
                                <p class="text-gray-700">{{ $info['value'] }}</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endsection
