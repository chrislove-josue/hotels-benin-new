<?php

namespace App\Providers;

use App\Models\EstablishmentType;
use App\Models\Locality;
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
        View::composer('*', function ($view) {
            $view->with('headerTypes', EstablishmentType::orderBy('name')->get());
            $view->with('headerLocalities', Locality::orderBy('name')->get());
        });
    }
}
