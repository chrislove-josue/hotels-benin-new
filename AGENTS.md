# AGENTS.md

## Vue d'ensemble

Application Laravel 13 unique (PHP 8.2+, Blade, MySQL) : plateforme de réservation d'hébergements au Bénin. Front-office vitrine **et** back-office complet (rôles `hote` et `admin`), réservations et paiement FedaPay.

- Tout le domaine est en français : routes (`/etablissements`, `/espace-hote`, `/evenements`...), statuts en base (`en_attente`, `confirmee`, `annulee`, `echouee`), libellés UI. Garder cette cohérence.
- ⚠️ Le README est obsolète (« vitrine seule, sans back-office »). Se fier à `routes/web.php` : il contient tout (back-office, réservations, paiement).
- Les fichiers `TODO*.md` à la racine sont des comptes-rendus de travaux déjà terminés (historique), pas du travail restant.

## Commandes

```bash
composer install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed    # jeu de démo complet (comptes de test, mot de passe : password)
php artisan storage:link      # obligatoire pour les images uploadées (/storage/uploads/...)
php artisan serve             # http://localhost:8000
```

## Pas de suite automatisée

Aucun dossier `tests/`, aucun `phpunit.xml` (malgré PHPUnit dans composer.json). Pour vérifier une modification :

1. `php -l <fichier>` sur chaque fichier PHP modifié ;
2. `php artisan view:cache` compile tous les gabarits Blade (attrape les erreurs de template) ;
3. `php artisan view:clear` ensuite ;
4. Test manuel via `php artisan serve` si le flux est concerné.

## Front-end : CDN, pas Vite (piège principal)

Les vues utilisent **Tailwind Play CDN + Alpine.js CDN + Bootstrap Icons CDN**, avec la config Tailwind inline dans chaque layout (`layouts/app.blade.php`, `layouts/backoffice.blade.php`, `layouts/voyageur.blade.php`). Vite/package.json existent mais ne servent plus : aucune vue n'utilise `@vite`. Ne pas lancer `npm run dev/build` en pensant compiler les assets.

- Les tokens design (`hb-blue` #1A3A6B, `hb-gold` #D4AF37, `hb-orange` #F59A23, polices Montserrat/Playfair Display) sont définis dans le `<script> tailwind.config` de chaque layout. Toute nouvelle couleur/utilitaire doit être ajoutée dans chaque layout, pas dans `tailwind.config.js`.
- Trois layouts distincts selon l'espace : `app` (vitrine publique), `voyageur` (espace voyageur), `backoffice` (hôte + admin).

## Auth, rôles et workflow d'approbation

- Rôles stockés sur `users.role` : `admin`, `hote`, `voyageur`. Aliases middleware définis dans `bootstrap/app.php` : `admin`, `hote`, `approved` (+ `auth` standard).
- Un compte hôte doit être approuvé par un admin (`users.is_approved`) avant d'accéder à son espace.
- Les modifications d'établissements/chambres par un hôte ne s'écrivent pas directement : elles créent des lignes dans `pending_establishment_changes` / `pending_room_changes`, approuvées/rejetées par l'admin (`AdminController::pendingChanges`). Respecter ce flux.

## Paiement FedaPay

- Clés via `config/services.php` : variables `FEDAPAY_PUBLIC_KEY`, `FEDAPAY_SECRET_KEY`, `FEDAPAY_ENVIRONMENT` (`sandbox` par défaut), `FEDAPAY_WEBHOOK_SECRET`. ⚠️ Absentes de `.env.example` : les ajouter manuellement.
- Webhook `POST /webhooks/fedapay` : exempté de CSRF dans `bootstrap/app.php` — ne pas casser cette exclusion.

## Pièges connus

- **Carbon 3** : `diffInDays()` retourne une valeur signée. `Reservation::nightsBetween()` utilise volontairement `$in->diffInDays($out)` (ordre inverse = négatif). Ne pas « corriger » cet ordre ni reformuler avec `max()` sur une valeur signée.
- Disponibilité des chambres : vérifiée via `Room::availableUnitsFor($checkIn, $checkOut, ?$excludeReservationId)` à la création de réservation ET au checkout/paiement (`TransactionController`). Toute nouvelle voie de réservation doit refaire ces vérifications.
- Images : trait `App\Traits\UploadsImages` — upload stocké dans `storage/app/public/uploads` (URL `/storage/uploads/...`), prioritaire sur le champ URL externe. Utiliser ce trait pour tout nouveau champ image.
