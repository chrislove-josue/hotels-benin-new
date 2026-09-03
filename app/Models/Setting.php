<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    protected $casts = [
        'value' => 'string',
    ];

    private const CACHE_KEY = 'settings.map';

    /**
     * Récupère la valeur d'un paramètre (avec cache).
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::allSettings()->get($key);

        if (is_null($value)) {
            return $default;
        }

        // Les valeurs « json://... » stockent des structures (tableaux, booléens).
        if (str_starts_with((string) $value, 'json://')) {
            return json_decode(substr((string) $value, 7), true);
        }

        return $value;
    }

    /**
     * Définit (crée ou met à jour) la valeur d'un paramètre puis purge le cache.
     */
    public static function set(string $key, mixed $value): void
    {
        $stored = is_scalar($value) || is_null($value)
            ? (is_bool($value) ? ($value ? 'true' : 'false') : (string) $value)
            : 'json://' . json_encode($value);

        static::updateOrCreate(['key' => $key], ['value' => $stored]);

        Cache::forget(static::CACHE_KEY);
    }

    /**
     * Tous les paramètres clé => valeur (chargés une seule fois pour la requête/cache).
     */
    public static function allSettings(): \Illuminate\Support\Collection
    {
        return Cache::rememberForever(static::CACHE_KEY, function () {
            return static::pluck('value', 'key');
        });
    }

    /**
     * Raccourci booléen : "true"/"1"/1 => true.
     */
    public static function boolean(string $key, bool $default = false): bool
    {
        $value = static::get($key);

        return match ($value) {
            'true', '1', 1, true => true,
            'false', '0', 0, false => false,
            default => $default,
        };
    }
}
