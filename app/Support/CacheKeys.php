<?php

namespace App\Support;

/**
 * Clés et durée de vie du cache applicatif.
 *
 * Centralise les noms de clés (et le TTL commun) pour ne pas les éparpiller
 * dans les controllers. Les données mises en cache ici sont les pages/query
 * publiques quasi statiques — un TTL court (10 min) évite de servir des
 * données trop obsolètes tout en déchargeant la base en cas de pic de trafic.
 */
class CacheKeys
{
    /**
     * Durée de vie par défaut (secondes) : 10 minutes.
     */
    public const TTL = 600;

    /**
     * Types d'établissements partagés dans le header / la recherche globale.
     */
    public const HEADER_TYPES = 'public.header_types';

    /**
     * Localités partagées dans le header / la recherche globale.
     */
    public const HEADER_LOCALITIES = 'public.header_localities';

    /**
     * Page d'accueil (établissements à la une, mieux notés, localités, types).
     */
    public const HOME = 'public.home';

    /**
     * Liste des établissements sans filtre — page 1.
     */
    public const ESTABLISHMENTS_INDEX_DEFAULT = 'public.establishments.index_default';

    /**
     * Préfixe des clés de détail d'un établissement (suffixe = slug).
     */
    public const ESTABLISHMENT_DETAIL_PREFIX = 'public.establishment.detail.';

    /**
     * Clé d'une liste de filtres (localités / types) de la page établissements.
     */
    public const ESTABLISHMENT_FILTERS = 'public.establishments.filters';
}
