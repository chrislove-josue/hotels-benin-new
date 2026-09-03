<?php

namespace App\Providers;

use App\Models\EstablishmentType;
use App\Models\Locality;
use App\Support\CacheKeys;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Partage les types d'établissements et les localités avec toutes les vues
        // (nécessaire pour le menu déroulant du header et la recherche globale).
        // Ces données changent rarement : mises en cache TTL pour éviter de
        // ré-exécuter les 2 requêtes à chaque rendu de page.
        View::composer('*', function ($view) {
            $view->with('headerTypes', Cache::remember(CacheKeys::HEADER_TYPES, CacheKeys::TTL, function () {
                return EstablishmentType::orderBy('name')->get();
            }));

            $view->with('headerLocalities', Cache::remember(CacheKeys::HEADER_LOCALITIES, CacheKeys::TTL, function () {
                return Locality::orderBy('name')->get();
            }));
        });
    }
}
