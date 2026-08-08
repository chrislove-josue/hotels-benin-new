# TODO — Vérification de disponibilité des chambres

## Objectif
Vérifier la disponibilité d'une chambre (quantité vs réservations en conflit) à chaque étape :
1. Création de la réservation ✅
2. Avant le paiement (page checkout) ✅
3. Au moment du paiement (process) ✅

## Étapes

### 1. Modèle `Room.php`
- [x] Ajouter méthode `availableUnitsFor($checkIn, $checkOut, ?int $excludeReservationId = null)`
      qui compte les réservations `confirmee` + `en_attente` chevauchant les dates et retourne `quantity - bookés`.
- [x] Ajouter relation `reservations()`.
- [x] Ajouter méthode `isUnavailableFor()`.

### 2. Contrôleur `ReservationController.php` (création)
- [x] Dans `store()` : vérifier `availableUnitsFor()` avant de créer. Si <= 0, retourner erreur "Chambre complète".

### 3. Contrôleur `TransactionController.php` (paiement)
- [x] Dans `checkout()` : vérifier disponibilité. Si indisponible, mettre la réservation en `annulee` et rediriger avec erreur.
- [x] Dans `process()` : re-vérifier avant de confirmer. Si indisponible, bloquer.
- [x] Helper `cancelUnavailable()` : annule la réservation + transaction `echouee`.

### 4. Vue `resources/views/reservations/checkout.blade.php`
- [x] Afficher bandeau de confirmation de disponibilité (avec nb d'unités restantes).

## Validation
- [x] `php -l` sur tous les fichiers modifiés (aucune erreur de syntaxe)
- [x] `php artisan view:cache` (tous les gabarits compilent)
- [x] Test manuel du flux
