<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Agrégateurs de paiement disponibles sur la plateforme et leur état.
 *
 * Deux agrégateurs sont gérés : `fedapay` et `kkiapay`. L'administrateur
 * active/désactive chacun d'eux et choisit lequel est utilisé par défaut au
 * paiement (le voyageur n'a pas le choix : il passe toujours par l'agrégateur
 * par défaut).
 */
class PaymentProvider
{
    public const FEDAPAY = 'fedapay';
    public const KKIAPAY = 'kkiapay';

    public const PROVIDERS = [self::FEDAPAY, self::KKIAPAY];

    /**
     * Agrégateurs actuellement activés par l'admin.
     */
    public static function enabledProviders(): array
    {
        $enabled = Setting::get('payment_providers_enabled', []);

        if (! is_array($enabled) || $enabled === []) {
            // Valeur par défaut : seuls les agrégateurs réellement configurés
            // (clés présentes) sont proposés. FedaPay l'est par défaut.
            return [self::FEDAPAY];
        }

        return array_values(array_intersect(self::PROVIDERS, $enabled));
    }

    /**
     * Agrégateur effectivement utilisé au paiement.
     */
    public static function defaultProvider(): string
    {
        $default = Setting::get('payment_provider_default', self::FEDAPAY);

        if (! in_array($default, self::enabledProviders(), true)) {
            $default = self::enabledProviders()[0] ?? self::FEDAPAY;
        }

        return $default;
    }

    /**
     * Un agrégateur est-il activé ?
     */
    public static function isEnabled(string $provider): bool
    {
        return in_array($provider, self::enabledProviders(), true);
    }

    /**
     * Clés FedaPay pour un environnement donné ("sandbox" ou "production").
     * Les clés sont stockées séparément par environnement (modifiables par
     * l'admin) avec repli .env.
     */
    public static function fedapayKey(string $key, ?string $env = null): ?string
    {
        $env = $env ?? static::fedapayEnvironment();

        $value = Setting::get('fedapay_' . $key . '_' . $env);

        if ($value) {
            return (string) $value;
        }

        return config('services.fedapay.' . $key);
    }

    /**
     * Environnement FedaPay : "sandbox" ou "production".
     */
    public static function fedapayEnvironment(): string
    {
        $env = strtolower((string) Setting::get('fedapay_environment', config('services.fedapay.environment', 'sandbox')));

        return in_array($env, ['production', 'live'], true) ? 'production' : 'sandbox';
    }

    /**
     * Clés KKiaPay pour un environnement donné ("sandbox" ou "production").
     */
    public static function kkiapayKey(string $key, ?string $env = null): ?string
    {
        $env = $env ?? static::kkiapayEnvironment();

        $value = Setting::get('kkiapay_' . $key . '_' . $env);

        if ($value) {
            return (string) $value;
        }

        return config('services.kkiapay.' . $key);
    }

    /**
     * Environnement KKiaPay : "sandbox" ou "production".
     */
    public static function kkiapayEnvironment(): string
    {
        $env = strtolower((string) Setting::get('kkiapay_environment', config('services.kkiapay.environment', 'sandbox')));

        return in_array($env, ['production', 'live'], true) ? 'production' : 'sandbox';
    }

    /**
     * Environnement KKiaPay en booléen "sandbox ?".
     */
    public static function kkiapaySandbox(): bool
    {
        return static::kkiapayEnvironment() === 'sandbox';
    }

    /**
     * URL de base de l'API KKiaPay selon l'environnement.
     */
    public static function kkiapayApiBase(): string
    {
        return static::kkiapaySandbox() ? 'https://api-sandbox.kkiapay.me' : 'https://api.kkiapay.me';
    }
}
