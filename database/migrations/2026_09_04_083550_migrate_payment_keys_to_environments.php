<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration
{
    /**
     * Migre les clés de paiement uniques vers des jeux de clés distincts par
     * environnement (sandbox / production).
     *
     * Règle : chaque agrégateur se voit attribuer ses clés actuelles selon son
     * environnement actif :
     *  - FedaPay actuellement en "production"  -> clés copiées vers *_production
     *  - KKiaPay actuellement en "sandbox"     -> clés copiées vers *_sandbox
     *
     * Les anciennes clés uniques sont ensuite supprimées (remplacées par le
     * nouveau schéma).
     */
    public function up(): void
    {
        $map = [
            // agrégateur => [ancienne clé => détails]
            'fedapay' => [
                'public_key'     => ['env' => 'production'],
                'secret_key'     => ['env' => 'production'],
                'webhook_secret' => ['env' => 'production'],
            ],
            'kkiapay' => [
                'public_key'  => ['env' => 'sandbox'],
                'private_key' => ['env' => 'sandbox'],
                'secret'      => ['env' => 'sandbox'],
            ],
        ];

        $rows = DB::table('settings')->whereIn('key', array_merge(
            ['fedapay_environment', 'kkiapay_environment'],
            collect($map)->flatMap(fn ($keys) => array_keys($keys))->map(fn ($k) => null)->all()
        ))->orWhere(function ($q) {
            $q->where('key', 'like', 'fedapay_%')
              ->orWhere('key', 'like', 'kkiapay_%');
        })->get()->keyBy('key');

        foreach ($map as $provider => $keys) {
            foreach ($keys as $oldKey => $meta) {
                $old = $rows->get("{$provider}_{$oldKey}");

                if (! $old || $old->value === null || $old->value === '') {
                    continue;
                }

                $env = $meta['env'];
                DB::table('settings')->updateOrInsert(
                    ['key' => "{$provider}_{$oldKey}_{$env}"],
                    ['value' => $old->value]
                );
            }
        }

        // Suppression des anciennes clés uniques (devenues obsolètes).
        foreach ($map as $provider => $keys) {
            foreach (array_keys($keys) as $oldKey) {
                DB::table('settings')->where('key', "{$provider}_{$oldKey}")->delete();
            }
        }

        Cache::forget('settings.map');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Rien à restaurer : cette migration est une transformation de données
        // sans retour arrière automatique fiable.
    }
};
