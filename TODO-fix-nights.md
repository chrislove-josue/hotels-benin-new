# TODO — Correction du calcul du nombre de nuits

## Diagnostic (confirmé par test)
`App\Models\Reservation::nightsBetween('2026-02-01', '2026-02-05')` retourne **1** au lieu de **4**.

Cause : Carbon 3 (Laravel 13 / PHP 8.4) — `$out->diffInDays($in)` retourne une valeur **signée** (`-4`).
`max(1, -4)` → `1`. Le nombre de nuits est donc toujours écrasé à 1, quel que soit le séjour.

## Étapes
- [x] 1. `app/Models/Reservation.php` : corriger `nightsBetween()` avec le diff absolu (`$in->diffInDays($out)`)
- [x] 2. Supprimer le fichier de test temporaire `test_nights.php`
- [x] 3. Vérifier avec `php artisan tinker` que `nightsBetween` retourne le bon nombre de nuits

## Résultat de la vérification
```
nightsBetween('2026-02-01', '2026-02-05') → 4   (correct, était 1 avant)
nightsBetween('2026-02-01', '2026-02-01') → 1   (minimum)
nightsBetween('2026-02-01', '2026-02-02') → 1   (minimum)
```
Le montant des réservations = prix × nuitées est désormais correctement calculé.

