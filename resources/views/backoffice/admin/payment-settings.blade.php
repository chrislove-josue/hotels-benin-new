@extends('layouts.backoffice')

@section('title', 'Paramètres de paiement — Administration')

@section('content')
<div class="mb-6">
    <nav class="mb-4 text-sm text-gray-500">
        <span class="font-semibold text-hb-blue">Paramètres de paiement</span>
    </nav>
    <h1 class="font-display text-3xl font-bold text-hb-blue">Agrégateurs de paiement</h1>
    <p class="mt-1 text-sm text-gray-500">Activez FedaPay et/ou KKiaPay et choisissez celui utilisé au paiement. Le voyageur passe toujours par l'agrégateur par défaut.</p>
</div>

<div class="card mx-auto max-w-3xl p-6 sm:p-8">
    <form action="{{ route('admin.payments.settings.update') }}" method="POST" class="space-y-6">
        @csrf

        @if($errors->any())
            <div class="rounded-xl bg-red-50 p-3 text-xs text-red-600">
                <ul class="space-y-1">
                    @foreach($errors->all() as $error)
                        <li>• {{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Agrégateurs --}}
        <div>
            <span class="mb-2 block text-xs font-semibold text-gray-500">Agrégateurs actifs</span>
            <div class="space-y-3">
                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-100 bg-hb-cream p-4">
                    <input type="checkbox" name="providers[]" value="fedapay" class="h-5 w-5 accent-hb-blue" @checked(in_array('fedapay', $enabled, true))>
                    <span class="text-sm text-gray-700">
                        <span class="font-semibold text-hb-blue">FedaPay</span>
                        <span class="block text-xs text-gray-500">
                            Environnement actif : <strong>{{ strtoupper($fedapay['environment'] ?? 'sandbox') }}</strong>
                        </span>
                    </span>
                </label>

                <label class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-100 bg-hb-cream p-4">
                    <input type="checkbox" name="providers[]" value="kkiapay" class="h-5 w-5 accent-hb-blue" @checked(in_array('kkiapay', $enabled, true))>
                    <span class="text-sm text-gray-700">
                        <span class="font-semibold text-hb-blue">KKiaPay</span>
                        <span class="block text-xs text-gray-500">
                            Environnement actif : <strong>{{ strtoupper($kkiapay['environment'] ?? 'sandbox') }}</strong>
                        </span>
                    </span>
                </label>
            </div>
        </div>

        {{-- Agrégateur par défaut --}}
        <div>
            <label class="mb-1 block text-xs font-semibold text-gray-500">Agrégateur utilisé par défaut</label>
            <select name="default_provider" class="input-field" required>
                <option value="fedapay" @selected($default === 'fedapay')>FedaPay</option>
                <option value="kkiapay" @selected($default === 'kkiapay')>KKiaPay</option>
            </select>
            <p class="mt-1 text-xs text-gray-400">Doit correspondre à un agrégateur activé ci-dessus.</p>
        </div>

        {{-- Clés FedaPay --}}
        <div class="rounded-xl border border-gray-100 bg-hb-cream p-4">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-semibold text-gray-500">Clés API FedaPay <span class="font-normal">(laisser vide pour conserver)</span></span>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="font-medium">Environnement actif</span>
                    <select name="fedapay_environment" class="input-field w-auto">
                        <option value="sandbox" @selected(($fedapay['environment'] ?? 'sandbox') === 'sandbox')>Sandbox (test)</option>
                        <option value="production" @selected(($fedapay['environment'] ?? 'sandbox') === 'production')>Production</option>
                    </select>
                </div>
            </div>

            @foreach(['sandbox' => 'Sandbox (test)', 'production' => 'Production'] as $envKey => $envLabel)
                <div class="mb-4 rounded-lg border border-dashed border-gray-200 bg-white p-3">
                    <span class="mb-2 block text-xs font-semibold text-hb-blue">{{ $envLabel }}</span>
                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">Clé publique</label>
                            <input type="text" name="fedapay_public_key_{{ $envKey }}" value="{{ old('fedapay_public_key_'.$envKey) }}" class="input-field" placeholder="{{ $fedapay[$envKey]['public_key'] ? '•••• (déjà configurée)' : 'Clé publique FedaPay '.$envKey }}">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">Clé secrète</label>
                            <input type="text" name="fedapay_secret_key_{{ $envKey }}" value="{{ old('fedapay_secret_key_'.$envKey) }}" class="input-field" placeholder="{{ $fedapay[$envKey]['secret_key'] ? '•••• (déjà configurée)' : 'Clé secrète FedaPay '.$envKey }}">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">Secret de webhook</label>
                            <input type="text" name="fedapay_webhook_secret_{{ $envKey }}" value="{{ old('fedapay_webhook_secret_'.$envKey) }}" class="input-field" placeholder="{{ $fedapay[$envKey]['webhook_secret'] ? '•••• (déjà configuré)' : 'Secret de webhook FedaPay '.$envKey }}">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Clés KKiaPay --}}
        <div class="rounded-xl border border-gray-100 bg-hb-cream p-4">
            <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                <span class="text-xs font-semibold text-gray-500">Clés API KKiaPay <span class="font-normal">(laisser vide pour conserver)</span></span>
                <div class="flex items-center gap-2 text-xs text-gray-500">
                    <span class="font-medium">Environnement actif</span>
                    <select name="kkiapay_environment" class="input-field w-auto">
                        <option value="sandbox" @selected(($kkiapay['environment'] ?? 'sandbox') === 'sandbox')>Sandbox (test)</option>
                        <option value="production" @selected(($kkiapay['environment'] ?? 'sandbox') === 'production')>Production</option>
                    </select>
                </div>
            </div>

            @foreach(['sandbox' => 'Sandbox (test)', 'production' => 'Production'] as $envKey => $envLabel)
                <div class="mb-4 rounded-lg border border-dashed border-gray-200 bg-white p-3">
                    <span class="mb-2 block text-xs font-semibold text-hb-blue">{{ $envLabel }}</span>
                    <div class="space-y-3">
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">Clé publique</label>
                            <input type="text" name="kkiapay_public_key_{{ $envKey }}" value="{{ old('kkiapay_public_key_'.$envKey) }}" class="input-field" placeholder="{{ $kkiapay[$envKey]['public_key'] ? '•••• (déjà configurée)' : 'Clé publique KKiaPay '.$envKey }}">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">Clé privée</label>
                            <input type="text" name="kkiapay_private_key_{{ $envKey }}" value="{{ old('kkiapay_private_key_'.$envKey) }}" class="input-field" placeholder="{{ $kkiapay[$envKey]['private_key'] ? '•••• (déjà configurée)' : 'Clé privée KKiaPay '.$envKey }}">
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-medium text-gray-500">Secrète</label>
                            <input type="text" name="kkiapay_secret_{{ $envKey }}" value="{{ old('kkiapay_secret_'.$envKey) }}" class="input-field" placeholder="{{ $kkiapay[$envKey]['secret'] ? '•••• (déjà configurée)' : 'Secrète KKiaPay '.$envKey }}">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex flex-col gap-3 pt-2 sm:flex-row">
            <button type="submit" class="btn-primary flex-1"><i class="bi bi-check-lg"></i> Enregistrer</button>
            <a href="{{ route('admin.dashboard') }}" class="btn-outline flex-1"><i class="bi bi-arrow-left"></i> Annuler</a>
        </div>
    </form>
</div>
@endsection
